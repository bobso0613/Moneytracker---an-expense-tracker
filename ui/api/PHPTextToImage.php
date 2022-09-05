<?php
/*phptext class, version 1.0
created by www.w3schools.in (Gautam kumar)
April 26, 2014
*/
class phptextClass
{
	public function phptext($text,$textColor,$backgroundColor='',$fontSize,$imgWidth,$imgHeight,$dir,$fileName)
	{
		/* settings */
		$font = '/Asimov.otf';/*define font*/
		$textColor=$this->hexToRGB($textColor);	
		//$textColor=$this->hexToRGB("#fff");
		
		$im = imagecreatetruecolor($imgWidth, $imgHeight);	
		$textColor = imagecolorallocate($im, $textColor['r'],$textColor['g'],$textColor['b']);	
		
		if($backgroundColor==''){/*select random color*/
			$colorCode=array('#56aad8', '#61c4a8', '#d3ab92', '#d9534f','#5cb85c', '#72490c', "#2b26af","#dbd711", "#a56908", "#0a9381", "#a316e0", "#534b8e", "#758e18", "#871414", "#6aa9c9", "#ff77ef", "#8fbc3a");
			$backgroundColor = $this->hexToRGB($colorCode[rand(0, count($colorCode)-1)]);
			$backgroundColor = imagecolorallocate($im, $backgroundColor['r'],$backgroundColor['g'],$backgroundColor['b']);
		}else{/*select background color as provided*/
			$backgroundColor = $this->hexToRGB($backgroundColor);
			$backgroundColor = imagecolorallocate($im, $backgroundColor['r'],$backgroundColor['g'],$backgroundColor['b']);
		}
		
		imagefill($im,0,0,$backgroundColor);	
		list($x, $y) = $this->ImageTTFCenter($im, $text, $font, $fontSize);	
		imagettftext($im, $fontSize, 0, $x, $y, $textColor, $font, $text);
		if(imagejpeg($im,$dir.$fileName,90)){/*save image as JPG*/
			//return json_encode(array('status'=>TRUE,'image'=>$dir.$fileName));
			return $dir.$fileName;
			imagedestroy($im);	
		}
	}
	
	/*function to convert hex value to rgb array*/
	protected function hexToRGB($colour)
	{
			if ( $colour[0] == '#' ) {
					$colour = substr( $colour, 1 );
			}
			if ( strlen( $colour ) == 6 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
			} elseif ( strlen( $colour ) == 3 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
			} else {
					return false;
			}
			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );
			return array( 'r' => $r, 'g' => $g, 'b' => $b );
	}
		
		
	/*function to get center position on image*/
	protected function ImageTTFCenter($image, $text, $font, $size, $angle = 8) 
	{
		$xi = imagesx($image);
		$yi = imagesy($image);
		$box = imagettfbbox($size, $angle, $font, $text);
		$xr = abs(max($box[2], $box[4]))+5;
		$yr = abs(max($box[5], $box[7]));
		$x = intval(($xi - $xr) / 2);
		$y = intval(($yi + $yr) / 2);
		return array($x, $y);	
	}

}
?>