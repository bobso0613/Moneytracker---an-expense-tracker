<?php
require_once("PrintoutFormatter.php");

class PrintoutTemplateFactory {

	public function get_policy_period_as_string($issue_date, $expiry_date) {
		$issue_date = strtotime($issue_date);
		$expiry_date = strtotime($expiry_date);
		$date_diff = abs($issue_date - $expiry_date);
		$period = floor( $date_diff / (60*60*24) );
		$period_in_s = '';

		if (fmod($period, 365) == 0) {
			$period = $period / 365;

			$period_in_s .= $period . ' ';

			$period_in_s .= '(' . PrintoutFormatter::getDataOrUnderline(
				$period, 30,
				PrintoutFormatter::DATA_TYPE_NUMBER_IN_WORDS, true, true, true
			) . ')';

			if ($period > 1) {
				$period_in_s .= ' YEARS';
			} else {
				$period_in_s .= ' YEAR';
			}
		} else {
			$period_in_s .= $period . ' ';

			$period_in_s .= '(' . PrintoutFormatter::getDataOrUnderline(
				$period, 30,
				PrintoutFormatter::DATA_TYPE_AMOUNT_IN_WORDS, true, true, true
			) . ')';

			if ($period > 1) {
				$period_in_s .= ' DAYS';
			} else {
				$period_in_s .= ' DAY';
			}
		}

		return $period_in_s;
	}

	// replaces [signor_name], [designation], [tin] by real data or line
	public function format_signatories($principals, $format, $custom_connector = null) {
		$find = array('[signor_name]', '[designation]', '[tin]', '[issue_date]', '[issue_place]');
		$replace = array(
			PrintoutFormatter::getDataOrUnderline(@$principals[0]['signor_name'], 60, 1),
			PrintoutFormatter::getDataOrUnderline(@$principals[0]['designation'], 30, 1),
			PrintoutFormatter::getDataOrUnderline(@$principals[0]['tin_no'], 30, 1),
			PrintoutFormatter::getDataOrUnderline(@$principals[0]['issue_date'], 30, 6),
			PrintoutFormatter::getDataOrUnderline(@$principals[0]['issue_place'], 30, 1)
		);

		$paragraph = str_replace($find, $replace, $format);

		for ($i = 1; $i < count($principals); $i++) {
			if ($custom_connector == null) {
				if ($i == count($principals) - 1) {
				$paragraph .= ' and ';
				} else {
					$paragraph .= ', ';
				}
			} else {
				$paragraph .= $custom_connector;
			}

			$replace = array(
				PrintoutFormatter::getDataOrUnderline($principals[$i]['signor_name'], 60, 1),
				PrintoutFormatter::getDataOrUnderline($principals[$i]['designation'], 30, 1),
				PrintoutFormatter::getDataOrUnderline($principals[$i]['tin_no'], 30, 1),
				PrintoutFormatter::getDataOrUnderline($principals[$i]['issue_date'], 30, 6),
				PrintoutFormatter::getDataOrUnderline($principals[$i]['issue_place'], 30, 1)
			);

			$paragraph .= str_replace($find, $replace, $format);
		}

		return $paragraph;
	}

	// replaces [signor_name], [designation] by real data or line
	public function format_signing($signatories, $format) {
		$find = array('[signor_name]', '[designation]');
		$signing_as_html = '';

		$count = 0;
		foreach($signatories as $signatory) {
			$count++;

			if ($count & 1) {
				$signing_as_html .= '<tr>';
			}

			$replace = array(
				$signatory['signor_name'],
				$signatory['designation']
			);

			$content = str_replace($find, $replace, $format);
			$signing_as_html .= $this->signing_field($content);

			if ( !($count & 1) ) {
				$signing_as_html .= '</tr>';
			}
		}

		if ($count & 1) {
			$signing_as_html .= '</tr>';
		}

		return $signing_as_html;
	}

	public function signing_field($content, $has_line = 1, $colspan = 1) {
		$line = '';

		if ($has_line) $line = PrintoutFormatter::getDataOrUnderline('', 50, 1, true, true, true);

		return '
			<td style="text-align: center;" colspan="' . $colspan . '">
				<br><br><br>
				' . $line . ' <br>
				' . $content . '
			</td>
		';
	}

	/*
	* @deprecated
	*/
	public function get_principals_as_paragraph($signatories) {
		$principals = array();
		foreach($signatories as $key => $signatory) {
			if ($signatory['signatory_type'] == '1') array_push($principals, $signatory);
		}

		if (count($principals) == 0 || count($principals) == 1) {
			return $this->get_one_principal($principals);
		} else {
			return $this->get_multiple_principals($principals);
		}
	}

	/*
	* @deprecated
	*/
	private function get_one_principal($principal) {
		$principals_as_paragraph = 'represented by ' .
			PrintoutFormatter::getDataOrUnderline(@$principal[0]['signor_name'], 60, 1)
			. ', ' .
			PrintoutFormatter::getDataOrUnderline(@$principal[0]['designation'], 30, 1);

		return $principals_as_paragraph;
	}

	/*
	* @deprecated
	*/
	private function get_multiple_principals($principals) {
		$principals_as_paragraph = 'represented by ' .
			PrintoutFormatter::getDataOrUnderline($principals[0]['signor_name'], 60, 1)
			. ', ' .
			PrintoutFormatter::getDataOrUnderline($principals[0]['designation'], 30, 1);

		for ($i = 1; $i < count($principals); $i++) {
			if ($i == count($principals) - 1) {
				$principals_as_paragraph .= ' and ';
			} else {
				$principals_as_paragraph .= ', ';
			}

			$principals_as_paragraph .=
				PrintoutFormatter::getDataOrUnderline($principals[$i]['signor_name'], 60, 1)
				. ', ' .
				PrintoutFormatter::getDataOrUnderline($principals[$i]['designation'], 30, 1);
		}

		return $principals_as_paragraph;
	}

	public function get_bond_legal_document($policy_details) {

		$signatories_as_paragraph = $this->get_signatories_as_paragraph(
			$policy_details['item']['signatories'],
			@$policy_details['item']['principal_tin']);

		$signors_as_paragraph = $this->get_signors_as_paragraph(
			$policy_details['item']['signatories']);

		$signors_signing_line = $this->get_signatories_as_html(
			$policy_details['item'],
			@$policy_details['item']['principal'],
			true, false, false, true);

		$lch_return = '
			<div>
				REPUBLIC OF THE PHILIPPINES) <br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) S.S.
				 <br>
				CITY OF MANILA' . PrintoutFormatter::formatDotsToLine('.......................') . ')
			</div>

			<p style="text-align: justified; text-indent: 50px">
				In the City of Manila, Philippines, this ' .
				PrintoutFormatter::formatDotsToLine('...........')
				. ' day of ' .
				PrintoutFormatter::formatDotsToLine('................................')
				. ', ' .
				PrintoutFormatter::formatDotsToLine('.........')
				. ' personally appeared before me ' .
				$signatories_as_paragraph
				. ' known to me to be the same persons who executed the foregoing instrument and who
				 acknowledged to me that the same is their free and voluntary act and deed and those
				  of the Corporation he/she/they represent(s).
			</p>

			<p style="text-align: justified; text-indent: 50px">
				IN WITNESS THEREOF, I have hereunto set my hand and affixed my notarial seal at the
				 place and date first above-written.
			</p>

			<table>
				<tr>
					<td></td>
					<td></td>
					<td style="text-align: center;">
						NOTARY PUBLIC <br>
						Until December 31, ' . PrintoutFormatter::formatDotsToLine('.........') . '
					</td>
				</tr>
			</table>

			<div>
				Doc. No.&nbsp; ' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Page No.&nbsp; ' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Book No. &nbsp;' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Series of  ' . PrintoutFormatter::formatDotsToLine('.................') . '
			</div>

			<div>
				REPUBLIC OF THE PHILIPPINES)  <br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) S.S.
				 <br>
				CITY OF MANILA' . PrintoutFormatter::formatDotsToLine('.......................') . ')
			</div>

			<div style="text-align: justified;">
				' . $signors_as_paragraph . '
				respectively of ALLIEDBANKERS INSURANCE CORPORATION, after having been duly sworn hereby
				 depose and say: that the said Corporation is duly organized and existing under laws
				  of the Philippines and duly authorized to execute and furnish surety bonds within
				  the Philippines, and that it is actually worth the amount specified in the
				  foregoing undertaking, to wit:
				' . PrintoutFormatter::getDataOrUnderline(@$policy_details['policy']['tsi_amount'], 90,
								PrintoutFormatter::DATA_TYPE_AMOUNT_IN_WORDS, true, true, true) .
				'&nbsp;
				(' . PrintoutFormatter::getDataOrUnderline(@$policy_details['policy']['currency']['short_name'], 10,
								PrintoutFormatter::DATA_TYPE_CHAR, true, true, true) .
					 PrintoutFormatter::getDataOrUnderline(@$policy_details['policy']['tsi_amount'], 30,
								PrintoutFormatter::DATA_TYPE_AMOUNT, true, true, true) . '),
				Philippine Currency, over and above all just debts and obligations and property exempt from execution.
			</div>
			<br>
			<table>
				<tr>
					<td></td>
					<td>
						' . $signors_signing_line . '
					</td>
				</tr>
			</table>

			<p style="text-indent: 50px; text-align:justified;">
				SUBSCRIBED AND SWORN to before me this ' . PrintoutFormatter::formatDotsToLine('..............') . ' day of ' . PrintoutFormatter::formatDotsToLine('...................................') . ' , ' . PrintoutFormatter::formatDotsToLine('..............') . '  at ' . PrintoutFormatter::formatDotsToLine('...........................................') . ' , Philippines, affiant ' . PrintoutFormatter::formatDotsToLine('..................................................................') . '  exhibiting to me his/her Community Tax Certificate No. ' . PrintoutFormatter::formatDotsToLine('.............................') . '  issued at ' . PrintoutFormatter::formatDotsToLine('.........................') . ' on ' . PrintoutFormatter::formatDotsToLine('.........................') . ' , ' . PrintoutFormatter::formatDotsToLine('........') . '  and affiant ' . PrintoutFormatter::formatDotsToLine('..............................................') . '  exhibiting to me his/her Community tax Certificate No. ' . PrintoutFormatter::formatDotsToLine('.............................') . '  issued at ' . PrintoutFormatter::formatDotsToLine('..........................................') . '  on ' . PrintoutFormatter::formatDotsToLine('.............................................') . ' , ' . PrintoutFormatter::formatDotsToLine('...........') . '
			</p>

			<table>
				<tr>
					<td></td>
					<td>
						<div style="text-align: center;">
							<br><br>
							NOTARY PUBLIC <br>
							Until December 31,' . PrintoutFormatter::formatDotsToLine('.........') . '
						</div>
					</td>
				</tr>
			</table>

			<div>
				Doc. No.&nbsp; ' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Page No.&nbsp; ' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Book No. &nbsp;' . PrintoutFormatter::formatDotsToLine('.................') . ' <br>
				Series of  ' . PrintoutFormatter::formatDotsToLine('.................') . '
			</div>
		';

		return $lch_return;
	}

	/*
	* @deprecated
	*/
	private function get_signatories_as_paragraph($signatories, $principal_corporation_tin) {
		$principals = array();
		$signors = array();

		foreach($signatories as $signatory) {
			switch ($signatory['signatory_type']) {
				case '1':
					array_push($principals, $signatory);
					break;
				case '2':
					array_push($signors, $signatory);
					break;
			}
		}

		$principals_as_paragraph = $this->get_multiple_signatories_with_tin($principals);
		$signors_as_paragraph = $this->get_multiple_signatories_with_tin($signors);

		$principals_as_paragraph .=
			' and that of the Corporation he/she/they represent(s) with Taxpayer’s Identification No.' .
			PrintoutFormatter::getDataOrUnderline($principal_corporation_tin, 30, 1)
			;

		$signors_as_paragraph .=
			' and that of the Corporation he/she/they represent(s) with Taxpayer’s Identification No.' .
			PrintoutFormatter::getDataOrUnderline('000-526-952', 30, 1)
			;

		return $principals_as_paragraph . ' and ' . $signors_as_paragraph;
	}

	/*
	* @deprecated
	*/
	private function get_multiple_signatories_with_tin($signatories) {
		$multiple_signatories_as_paragraph = '' .
			PrintoutFormatter::getDataOrUnderline(@$signatories[0]['signor_name'], 60, 1)
			. ' exhibiting to me his/her Taxpayer’s Identification No. ' .
			PrintoutFormatter::getDataOrUnderline(@$signatories[0]['tin_no'], 30, 1);

		// loop signatories
		for ($i = 1; $i < count($signatories); $i++) {
			$nouns_connector = ', ';

			if ( $i == (count($signatories) - 1) ) {
				$nouns_connector = ' and ';
			}

			$multiple_signatories_as_paragraph .=
				$nouns_connector .
				PrintoutFormatter::getDataOrUnderline($signatories[$i]['signor_name'], 60, 1)
				. ' exhibiting to me his/her Taxpayer’s Identification No. ' .
				PrintoutFormatter::getDataOrUnderline($signatories[$i]['tin_no'], 30, 1);
		}

		return $multiple_signatories_as_paragraph;
	}

	/*
	* @deprecated
	*/
	private function get_signors_as_paragraph($signatories) {
		$signors = array();

		foreach($signatories as $signatory) {
			if ($signatory['signatory_type'] == '2') {
				array_push($signors, $signatory);
			}
		}

		if (count($signors) == 0 || count($signors) == 1 ) {
			return $this->get_one_signor_with_designation(@$signors[0]);
		} else {
			return $this->get_multiple_signors_with_designation($signors);
		}
	}

	/*
	* @deprecated
	*/
	private function get_one_signor_with_designation($signor) {
		return '
			I, ' .
			PrintoutFormatter::getDataOrUnderline(@$signor['signor_name'], 60, 1)
			 . ' in my capacity as ' .
			PrintoutFormatter::getDataOrUnderline(@$signor['designation'], 30, 1);
	}

	/*
	* @deprecated
	*/
	private function get_multiple_signors_with_designation($signors) {
		if (count($signors) == 0) {
			return "";
		}

		$multiple_signors_as_paragraph = 'We, ';

		$multiple_signors_as_paragraph .= '' .
		PrintoutFormatter::getDataOrUnderline($signors[0]['signor_name'], 60, 1)
		. '';

		// loop signors to layout their names
		for ($i = 1; $i < count($signors); $i++) {
			$nouns_connector = ', ';

			if ( $i == (count($signors) - 1) ) {
				$nouns_connector = ' and ';
			}

			$multiple_signors_as_paragraph .=
				$nouns_connector . PrintoutFormatter::getDataOrUnderline($signors[$i]['signor_name'], 60, 1);
		}

		$multiple_signors_as_paragraph .= ' in our capacity as ';

		$multiple_signors_as_paragraph .= '' .
		PrintoutFormatter::getDataOrUnderline($signors[0]['designation'], 30, 1)
		. '';

		// loop signors to layout their designations
		for ($i = 1; $i < count($signors); $i++) {
			$nouns_connector = ', ';

			if ( $i == (count($signors) - 1) ) {
				$nouns_connector = ' and ';
			}

			$multiple_signors_as_paragraph .=
				$nouns_connector . PrintoutFormatter::getDataOrUnderline($signors[$i]['designation'], 30, 1);
		}

		return $multiple_signors_as_paragraph;
	}

	public function get_signatories_with_tin_as_html($item) {
		$signatories = $item['signatories'];

		$signatory_rows = '';

		foreach($signatories as $signatory) {
			if ($signatory['signatory_type'] == '2') {
				$signatory_rows = '
					<tr>
						<td>' . $this->get_data_or_line($signatory['signor_name'], 90, 1, true, false, true) . '</td>
						<td>' . $this->get_data_or_line('TIN NO. ' . $signatory['tin_no'], 90, 1, true, false, false) . '</td>
					</tr>
				';
			}
		}

		return '
		<table cellspacing="10">
			<tr>
				<th style="font-weight: bold;">NAME</th>
				<th>TAXPAYER\'S INDENTIFICATION NO.</th>
			</tr>
			' . $signatory_rows . '
		</table>
		';
	}

	public function get_signatories_as_html($item, $principal_header = "", $is_with_co_signor = true,
		$is_with_witness = true, $is_with_principal = true, $is_set_as_blank = false) {
		$signatories = $item['signatories'];

		$principal_count = 0;
		$co_signor_count = 0;
		$witness_count = 0;

		$principals_as_html = '';
		$co_signor_as_html = '';
		$witnesses_as_html = '';

		if (count($signatories) == 0) {
			$principals_as_html .= '<tr>' . $this->signatory_blank_field() . '</tr>';
			$co_signor_as_html .= '<tr>' . $this->signatory_blank_field() . '</tr>';
		}

		foreach($signatories as $signatory) {
			switch($signatory['signatory_type']) {
				case '1':
					$principal_count++;
					if ($principal_count & 1) {
						$principals_as_html .= '<tr>';
					}

					if ($is_set_as_blank) {
						$principals_as_html .= $this->signatory_blank_field();
					} else {
						$principals_as_html .= $this->signatory_field($signatory['signor_name'], $signatory['designation']);
					}

					if ( !($principal_count & 1) ) {
						$principals_as_html .= '</tr>';
					}
					break;
				case '2':
					$co_signor_count++;
					if ($co_signor_count & 1) {
						$co_signor_as_html .= '<tr>';
					}

					if ($is_set_as_blank) {
						$co_signor_as_html .= $this->signatory_blank_field();
					} else {
						$co_signor_as_html .= $this->signatory_field($signatory['signor_name'], $signatory['designation']);
					}

					if ( !($co_signor_count & 1) ) {
						$co_signor_as_html .= '</tr>';
					}
					break;
				case '3':
					$witness_count++;
					if ($witness_count & 1) {
						$witnesses_as_html .= '<tr>';
						$witnesses_as_html .= $this->witness_field($signatory['signor_name']);
					}

					if ( !($witness_count & 1) ) {
						$witnesses_as_html .= '<td></td>';
						$witnesses_as_html .= $this->witness_field($signatory['signor_name']);
						$witnesses_as_html .= '</tr>';
					}
					break;
			}
		}

		if ($principal_count & 1) {
			$principals_as_html .= '</tr>';
		}

		if ($co_signor_count & 1) {
			$co_signor_as_html .= '</tr>';
		}

		if ($witness_count & 1) {
			$witnesses_as_html .= '<td></td>';
			$witnesses_as_html .= '</tr>';
		}

		$principals_td = '
		<td>
			<div style="text-align: center;">
				' . $this->get_data_or_line($principal_header, 60, 1, true, true, false) . '
			</div>
			<div style="text-align: left;">by</div>
			<table>
				' . $principals_as_html .  '
			</table>
		</td>';

		$co_signors_td = '
		<td>
			<div style="text-align: center; font-weight: bold;">
				ALLIEDBANKERS INSURANCE CORPORATION
			</div>
			<div style="text-align: left;">by</div>
			<table>
				' . $co_signor_as_html .  '
			</table>
		</td>
		';

		$witness_tr = '
			<tr>
				<td><br><br><br></td>
				<td style="text-align: center;">
					Signed in the Presence of
				</td>
				<td><br><br><br></td>
			</tr>
			' . $witnesses_as_html . '
		';

		if (!$is_with_co_signor) {
			$co_signors_td = '';
		}

		if (!$is_with_witness) {
			$witness_tr = '';
		}

		if (!$is_with_principal) {
			$principals_td = '';
		}

		$lch_return = '
		<div nobr="true">
			<table>
				<tr>
					' . $principals_td . '
					' . $co_signors_td . '
				</tr>
			</table>
			<table>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				' . $witness_tr . '
			</table>
		</div>
		';

		return $lch_return;
	}

	private function signatory_field($person_name, $designation, $colspan = 1) {
		return '
			<td style="text-align: center;" colspan="' . $colspan . '">
				' . $this->get_data_or_line($person_name, 30, 1, true, true, false) . '<br>
				' . $this->get_data_or_line($designation, 30, 1, false, false, false) . '<br><br>
			</td>
		';
	}

	private function signatory_blank_field($colspan = 1) {
		return '
			<td style="text-align: center;" colspan="' . $colspan . '">
				' . $this->to_line('...........................') . '
			</td>
		';
	}

	private function witness_field($person_name) {
		return '<td style="text-align: center; border-bottom: 2px dashed black; font-weight: bold;">' . $person_name . '</td>';
	}

	// type= (1: character, 2: amount, 3: amount in words, 4: date)
	private function get_data_or_line($data, $line_count, $type = 1, $is_capital = true, $is_bold = true, $is_underlined = false) {
		$to_return = "";

		if ($data != "" || $data != 0) {

			switch($type) {
				case 1:
					$to_return =  $data;
					break;
				case 2:
					$to_return =  $this->format_number($data);
					break;
				case 3:
					$to_return =  $this->num_to_words($data);
					break;
				case 4:
					$to_return =  date_format(date_create($data), "jS \of F, Y	");
					break;
				default:
					$to_return =  $data;
					break;
			}
		} else {
			switch($type) {
				case 4:
					$to_return =  $this->to_line("________") . " day of " . $this->to_line("__________________") . ", " . $this->to_line("__________");
					break;
				default:
					$line = "";
					for ($i = 0; $i < $line_count; $i++) {
						$line .= "_";
					}
					$to_return =  $this->to_line($line);
					break;
			}
		}

		if ($is_capital) {
			$to_return = strtoupper($to_return);
		}

		if ($is_underlined) {
			$to_return = "<u>" . $to_return . "</u>";
		}

		if ($is_bold) {
			$to_return = "<strong>" . $to_return . "</strong>";
		}

		return $to_return;
	}

	private function to_line($dots) {
		$dots = '<span style="font-weight: bold; letter-spacing: -3px;">' . $dots . '</span>';

		return $dots;
	}

	private function format_date($to_format) {
		return ($to_format === "0000-00-00" ? "" : date_format(date_create($to_format),"F d, Y"));
	}

	private function format_number($to_format) {
		if ($to_format == "" || !is_numeric($to_format)) {
			return $to_format;
		}
		return number_format($to_format, 2);
	}

	// should be in another file?
	private function num_to_words($num){
		$num = str_replace(",", "", $num);

		if (!is_numeric($num)) {
			return $num;
		}

		if ($num == 0) {
			return "";
		}

		$decones = array(
		            '01' => "One",
		            '02' => "Two",
		            '03' => "Three",
		            '04' => "Four",
		            '05' => "Five",
		            '06' => "Six",
		            '07' => "Seven",
		            '08' => "Eight",
		            '09' => "Nine",
		            '10' => "Ten",
		            '11' => "Eleven",
		            '12' => "Twelve",
		            '13' => "Thirteen",
		            '14' => "Fourteen",
		            '15' => "Fifteen",
		            '16' => "Sixteen",
		            '17' => "Seventeen",
		            '18' => "Eighteen",
		            '19' => "Nineteen"
		            );
		$ones = array(
		            0 => " ",
		            1 => "One",
		            2 => "Two",
		            3 => "Three",
		            4 => "Four",
		            5 => "Five",
		            6 => "Six",
		            7 => "Seven",
		            8 => "Eight",
		            9 => "Nine",
		            10 => "Ten",
		            11 => "Eleven",
		            12 => "Twelve",
		            13 => "Thirteen",
		            14 => "Fourteen",
		            15 => "Fifteen",
		            16 => "Sixteen",
		            17 => "Seventeen",
		            18 => "Eighteen",
		            19 => "Nineteen"
		            );
		$tens = array(
		            0 => "",
		            2 => "Twenty",
		            3 => "Thirty",
		            4 => "Forty",
		            5 => "Fifty",
		            6 => "Sixty",
		            7 => "Seventy",
		            8 => "Eighty",
		            9 => "Ninety"
		            );
		$hundreds = array(
		            "Hundred",
		            "Thousand",
		            "Million",
		            "Billion",
		            "Trillion",
		            "Quadrillion"
		            ); //limit t quadrillion
		$num = number_format($num,2,".",",");
		$num_arr = explode(".",$num);
		$wholenum = $num_arr[0];
		$decnum = $num_arr[1];
		$whole_arr = array_reverse(explode(",",$wholenum));
		krsort($whole_arr);
		$rettxt = "";
		foreach($whole_arr as $key => $i){
			while (strlen($i) < 3) {
				$i = '0' . $i;
			}

		    if($i < 20){
		    	if (substr($i,1,1) == 1) {
		        	$rettxt .= " ".$decones[substr($i,1,2)];
		        } else {
		        	$rettxt .= " ".$tens[substr($i,1,1)];
			        $rettxt .= " ".$ones[substr($i,2,1)];
		        }
		        //$rettxt .= isset($ones[$i]) ? $ones[$i] : "";
		    }
		    /*elseif($i < 10 && $i > 0) {
		    	$rettxt .= " ". $ones[substr($i,0,1)];
		    }*/
		    elseif($i < 100){
		        $rettxt .= $tens[substr($i,1,1)];
		        $rettxt .= " ".$ones[substr($i,2,1)];
		    }
		    else{
		        $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
		        if (substr($i,1,1) == 1) {
		        	$rettxt .= " ".$decones[substr($i,1,2)];
		        } else {
		        	$rettxt .= " ".$tens[substr($i,1,1)];
			        $rettxt .= " ".$ones[substr($i,2,1)];
		        }
		    }
		    if($key > 0){
		        $rettxt .= " ".$hundreds[$key]." ";
		    }

		}
		$rettxt = $rettxt." PESO";

		if($decnum > 0){
		    $rettxt .= " And ";
		    if($decnum < 20){
		        $rettxt .= $decones[$decnum];
		    }
		    elseif($decnum < 100){
		        $rettxt .= $tens[substr($decnum,0,1)];
		        $rettxt .= " ".$ones[substr($decnum,1,1)];
		    }
		    $rettxt = $rettxt." Centavos";
		}
		return $rettxt . " Only";
	}

}

?>