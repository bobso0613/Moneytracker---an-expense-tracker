<?php
class PrintoutFormatter {

	const DATA_TYPE_CHAR = 1;
	const DATA_TYPE_AMOUNT = 2;
	const DATA_TYPE_AMOUNT_IN_WORDS = 3;
	const DATA_TYPE_DATE = 4;
	const DATA_TYPE_NUMBER_IN_WORDS = 5;
	const DATA_TYPE_REGULAR_DATE = 6;
	const DATA_TYPE_SHORTCUT_DATE = 7;

	/*
	* Gets data, if data is null, then returns underline
	* If data is not null, then returns formatted data based on its type.
	*/
	static public function getDataOrUnderline($data, $line_count,
		$type = 1, $is_capital = true, $is_bold = true, $is_underlined = false) {

		$to_return = "";

		if ($data != "" || $data != 0) {
			$to_return = self::formatDataByType($data, $type);
		} else {
			$to_return = self::formatLineByType($data, $line_count);
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

	static private function formatDataByType($data, $type) {
		$to_return = "";
		switch($type) {
			case self::DATA_TYPE_CHAR:
				$to_return =  $data;
				break;
			case self::DATA_TYPE_AMOUNT:
				$to_return =  self::formatNumberToAmount($data);
				break;
			case self::DATA_TYPE_AMOUNT_IN_WORDS:
				$to_return =  self::formatAmountToWords($data);
				break;
			case self::DATA_TYPE_DATE:
				$to_return =  self::formatStringToFormalDate($data);
				break;
			case self::DATA_TYPE_REGULAR_DATE:
				$to_return =  self::formatStringToDate($data);
				break;
			case self::DATA_TYPE_SHORTCUT_DATE:
				$to_return =  self::formatStringToShortcutDate($data);
				break;
			case self::DATA_TYPE_NUMBER_IN_WORDS:
				$to_return =  self::formatNumberToWords($data);
				break;
			default:
				$to_return =  $data;
				break;
		}

		return $to_return;
	}

	static public function formatStringToFormalDate($timestamp) {
		return date_format(date_create($timestamp), "jS \of F, Y	");
	}

	static public function formatStringToDate($timestamp) {
		return date_format(date_create($timestamp), "F d, Y");
	}

	static public function formatStringToShortcutDate($timestamp) {
		return date_format(date_create($timestamp), "M. d, Y");
	}

	static public function formatNumberToAmount($number) {
		if ($number == "") {
			return "";
		}

		if (!is_numeric($number)) {
			return $number;
		}

		return number_format($number, 2);
	}

	static public function formatNumberToRate($number) {
		if ($number == "") {
			return "";
		}

		if (!is_numeric($number)) {
			return $number;
		}

		return number_format($number, 6);
	}
	
	static public function formatAmountToWords($num) {
		if ( !is_numeric($num) ) {
			$num = str_replace(',', '', $num);
			if ( !is_numeric($num) ) {
				return $num;
			}
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

		$rettxt = "";
		if ($num<0.0000000000){
			$rettxt = ""; // Negative
		} // if ($num<0.0000000000){
		$num = number_format(abs($num),2,".",",");
		$num_arr = explode(".",$num);
		$wholenum = $num_arr[0];
		$decnum = $num_arr[1];
		$whole_arr = array_reverse(explode(",",$wholenum));
		krsort($whole_arr);
		
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

		    if($key > 0 && $i > 0){
		        $rettxt .= " ".$hundreds[$key]." ";
		    }

		}
		

		if($decnum > 0){
		    $rettxt .= " And " . $decnum . '/100';
		    /*if($decnum < 20){
		        $rettxt .= $decones[$decnum];
		    }
		    elseif($decnum < 100){
		        $rettxt .= $tens[substr($decnum,0,1)];
		        $rettxt .= " ".$ones[substr($decnum,1,1)];
		    }*/

		    
		} 

		$rettxt = $rettxt." ONLY";

		return $rettxt;
	}

	static public function formatNumberToWords($num) {
		if ( !is_numeric($num) ) {
			$num = str_replace(',', '', $num);
			if ( !is_numeric($num) ) {
				return $num;
			}
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

		    if($key > 0 && $i > 0){
		        $rettxt .= " ".$hundreds[$key]." ";
		    }

		}

		return $rettxt;
	}

	static public function formatDotsToLine($dots) {
		$dots = '<span style="font-weight: bold; letter-spacing: -3px;">' . $dots . '</span>';

		return $dots;
	}

	static private function formatLineByType($type, $line_count) {
		$to_return = "";

		switch($type) {
			case self::DATA_TYPE_DATE:
				$to_return =  self::formatDotsToLine("________") . " day of " . self::formatDotsToLine("__________________") . ", " . self::formatDotsToLine("__________");
				break;
			default:
				$line = "";
				for ($i = 0; $i < $line_count; $i++) {
					$line .= "_";
				}
				$to_return =  self::formatDotsToLine($line);
				break;
		}

		return $to_return;
	}

}
?>