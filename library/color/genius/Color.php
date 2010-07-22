<?php

/** 
* Short Description
*
* Long Description
* @package ColorConvertor
* @author Matt Mueller
*/

class Color
{
	private $rgb;
	private $hsb;
	private $hex;
	private $cmy;
	
	function __construct($color) {
		$color = trim($color);
		
		if(preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)){
			$this->hex($color);
		} elseif(preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)) {
			$this->rgb($color);
		} else {
			throw new Exception("Unable to add Color", 1);
		}
	}
	
	public function rgb($mixed = null) {
		if(!isset($mixed)) {
			return $this->rgb;
		}
		
		if(is_numeric($mixed)) {
			$rgb = explode(' ', $this->rgb);
			return $rgb[$mixed];
		} elseif(strlen($mixed) == 1) {
			$rgb = explode(' ', $this->rgb);
			if($mixed == 'r') {
				return $rgb[0];
			} elseif($mixed == 'g') {
				return $rgb[1];
			} elseif($mixed == 'b') {
				return $rgb[2];
			}
		} else {
			$rgb = preg_replace('/[,. ]+/', ' ', $mixed);
			$rgb = explode(' ', $rgb);
			
			foreach ($rgb as $i => $value) {
				$rgb[$i] = ($value > 255) ? 255 : $value;
			}
			
			foreach ($rgb as $i => $value) {
				$rgb[$i] = ($value < 0) ? 0 : $value;
			}
			
			$this->rgb = implode(' ', $rgb);
			
			$cmy = array();
			foreach ($rgb as $i => $value) {
				$cmy[] = round((255 - $value) / 255, 2);
			}
			
			$this->cmy = implode(' ', $cmy);
			$this->hex = $this->rgb_hex($this->rgb);
	      	$this->hsb = $this->rgb_hsb($this->rgb);
	
			return $this;
		}
	}
	
	public function add($color) {
		$additive = new Color($color);
		
		$rgb1 = explode(' ', $this->rgb());		
		$rgb2 = explode(' ', $additive->rgb());
	
		$rgb = array();
		$rgb[0] = ($rgb1[0] + $rgb2[0] > 255) ? 255 : $rgb1[0] + $rgb2[0];
		$rgb[1] = ($rgb1[1] + $rgb2[1] > 255) ? 255 : $rgb1[1] + $rgb2[1];
		$rgb[2] = ($rgb1[2] + $rgb2[2] > 255) ? 255 : $rgb1[2] + $rgb2[2];
		$rgb = implode(' ', $rgb);
		
		// Will automatically update the rest of the formats.
		$this->rgb($rgb);
		
		return $this;
	}
	
	public function subtract($color) {
		$subtract = new Color($color);
		
		$rgb1 = explode(' ', $this->rgb());		
		$rgb2 = explode(' ', $subtract->rgb());
	
		$rgb = array();
		$rgb[0] = ($rgb1[0] - $rgb2[0] < 0) ? 0 : $rgb1[0] - $rgb2[0];
		$rgb[1] = ($rgb1[1] - $rgb2[1] < 0) ? 0 : $rgb1[1] - $rgb2[1];
		$rgb[2] = ($rgb1[2] - $rgb2[2] < 0) ? 0 : $rgb1[2] - $rgb2[2];
		$rgb = implode(' ', $rgb);
		
		// Will automatically update the rest of the formats.
		$this->rgb($rgb);
		
		return $this;
	}
	
	public function cmy($mixed = null) {
		if(!isset($mixed)) {
			return $this->cmy;
		}
		
		if(is_numeric($mixed)) {
			$cmy = explode(' ', $this->cmy);
			return $rgb[$mixed];
		} elseif(strlen($mixed) == 1) {
			$cmy = explode(' ', $this->cmy);
			if($mixed == 'c') {
				return $cmy[0];
			} elseif($mixed == 'm') {
				return $cmy[1];
			} elseif($mixed == 'y') {
				return $cmy[2];
			}
		} else {
			$cmy = preg_replace('/[, ]+/', ' ', $mixed);
			$cmy = explode(' ', $cmy);
			
			foreach ($cmy as $i => $value) {
				$cmy[$i] = ($value > 1) ? 1 : $value;
			}
			
			foreach ($cmy as $i => $value) {
				$cmy[$i] = ($value < 0) ? 0 : $value;
			}
			
			$this->cmy = implode(' ', $cmy);
			
			$rgb = array();
			foreach ($cmy as $i => $value) {
				$rgb[] = 255 - 255 * $value;
			}
			
			$this->rgb = implode(' ', $cmy);
			$this->hex = $this->rgb_hex($this->rgb);
	      	$this->hsb = $this->rgb_hsb($this->rgb);
	
			return $this;
		}
	}
	
	public function hex($mixed = null) {
		if(!isset($mixed)) {
			return $this->hex;
		}
		
		if(is_numeric($mixed) && $mixed >= 0 && $mixed <= 2) {
			$hex = explode(' ', $this->hex);
			return $hex[$mixed];
		} else {
			$mixed = trim($mixed,'#');
			
			// Format hex
			$hex = array();
			$hex[] = substr($mixed, 0, 2);
			$hex[] = substr($mixed, 2, 2);
			$hex[] = substr($mixed, 4, 2);
			
			$this->hex = strtoupper(implode(' ', $hex));
			$this->rgb = $this->hex_rgb($this->hex);
			
			$cmy = array();
			foreach (explode(' ', $this->rgb) as $i => $value) {
				$cmy[] = round((255 - $value)/255, 2);
			}

			$this->cmy = implode(' ', $cmy);
			$this->hsb = $this->rgb_hsb($this->rgb);
			
			return $this;
		}
	}
	
	public function hsb($mixed = null) {
		if(!isset($mixed)) {
			return $this->hsb;
		}
		
		if(is_numeric($mixed)) {
			$hsb = explode(' ', $this->hsb);
			return $hsb[$mixed];
		} elseif(strlen($mixed) == 1) {
			$hsb = explode(' ', $this->hsb);
			if($mixed == 'h') {
				return $hsb[0];
			} elseif($mixed == 's') {
				return $hsb[1];
			} elseif($mixed == 'b') {
				return $hsb[2];
			}
		} else {
			$hsb = preg_replace('/[,. ]+/', ' ', $mixed);
			$hsb = explode(' ', $hsb);
			
			$hsb[0] = max(0, $hsb[0]);
			$hsb[0] = min(360, $hsb[0]);
			$hsb[1] = max(0, $hsb[1]);
			$hsb[1] = min(100, $hsb[1]);
			$hsb[2] = max(0, $hsb[2]);
			$hsb[2] = min(100, $hsb[2]);
			
			$this->hsb = implode(' ', $hsb);
			$this->rgb = $this->hsb_rgb($this->hsb);
			$this->hex = $this->rgb_hex($this->rgb);
	
			return $this;
		}
	}
	
	private function hex_rgb($hex) {
		$c = preg_replace('/[,. #]+/', '', $hex);
	    $l = strlen($c) == 3 ? 1 : (strlen($c) == 6 ? 2 : false);
		
		$out = array();
		if($l) {
		   $out['red'] = hexdec(substr($c, 0,1*$l));
		   $out['green'] = hexdec(substr($c, 1*$l,1*$l));
		   $out['blue'] = hexdec(substr($c, 2*$l,1*$l));
		} else {
			$out = false;
		}
		
		return implode(' ', $out);
	}
	
	private function rgb_hex($rgb) {
		$e = explode(' ', $rgb);
		if(count($e) != 3) return false;

		$out = array();
		for($i = 0; $i<3; $i++)
		  $e[$i] = dechex(($e[$i] <= 0)?0:(($e[$i] >= 255)?255:$e[$i]));

		for($i = 0; $i<3; $i++)
		  $out[] = ((strlen($e[$i]) < 2)?'0':'').$e[$i];

		$out = implode(' ', $out);
		$out = strtoupper($out);

	   	return $out;
	}
	
	function hsb_rgb ($hsb) {
		$RGB = array();

		$hsb = explode(' ', $hsb);

		$H = $hsb[0] / 360;
		$S = $hsb[1] / 100;
		$V = $hsb[2] / 100;

		if($S == 0)
		{
		$R = $G = $B = $V * 255;
		}
		else
		{
		$var_H = $H * 6;
		$var_i = floor( $var_H );
		$var_1 = $V * ( 1 - $S );
		$var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) );
		$var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) );

		if ($var_i == 0) { $var_R = $V ; $var_G = $var_3 ; $var_B = $var_1 ; }
		else if ($var_i == 1) { $var_R = $var_2 ; $var_G = $V ; $var_B = $var_1 ; }
		else if ($var_i == 2) { $var_R = $var_1 ; $var_G = $V ; $var_B = $var_3 ; }
		else if ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2 ; $var_B = $V ; }
		else if ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1 ; $var_B = $V ; }
		else { $var_R = $V ; $var_G = $var_1 ; $var_B = $var_2 ; }

		$R = $var_R * 255;
		$G = $var_G * 255;
		$B = $var_B * 255;
		}

		$RGB['R'] = round($R);
		$RGB['G'] = round($G);
		$RGB['B'] = round($B);

		return implode(' ', $RGB);
	}
	
	private function rgb_hsb($rgb) {
		$rgb = explode(' ', $rgb);

		$r = $rgb[0] / 255.0;
		$g = $rgb[1] / 255.0;
		$b = $rgb[2] / 255.0;
		
		$H = 0;
		$S = 0;
		$B = 0;

		if($r == 0 && $g == 0 && $b == 0) {
			return '0 0 0';
		}
		
		$min = min(min($r, $g),$b);
		$max = max(max($r, $g),$b);
		$delta = $max - $min;

		$B = $max;

		if($delta == 0)
		{
			$H = 0;
			$S = 0;
		}
		else
		{
			$S = $delta / $max;

			$dR = ((($max - $r) / 6) + ($delta / 2)) / $delta;
			$dG = ((($max - $g) / 6) + ($delta / 2)) / $delta;
			$dB = ((($max - $b) / 6) + ($delta / 2)) / $delta;

			if ($r == $max)
				$H = $dB - $dG;
			else if($g == $max)
				$H = (1/3) + $dR - $dB;
			else
				$H = (2/3) + $dG - $dR;

			if ($H < 0)
				$H += 1;
			if ($H > 1)
				$H -= 1;
		}
		
		$out = array();
		$out['H'] = round($H*360);
		$out['S'] = round($S*100);
		$out['B'] = round($B*100);
		
		return implode(' ', $out);
	}
	
}

?>