<?php

/** 
* Short Description
*
* Long Description
* @package Color_Genius
* @author Matt Mueller
*/
require_once(dirname(__FILE__).'/Color.php');
require_once('/Users/Matt/Sites/Scarlet/Scarlet.php');

class Color_Genius extends Tag 
{
	private $cc;
	private $color;
	private $colors = array();
	private $color_name = '';
	
	function setup() {
		$this->defaults('color');
		$this->colors = $this->_set_initial_colors();
		
		// Check color dictionary to see if its in there.
		if(isset($this->colors[$this->arg('color')])) {
			$hex = $this->colors[$this->arg('color')];
			$this->color_name = $this->arg('color');
			$this->arg('color', $hex);
		}
		
		$this->cc = new Color($this->arg('color'));
		$this->color = $this->cc;
	}
	
	public function color($type = 'hex') {
		if($type == 'name') {
			if($this->color_name) {
				return $this->color_name;
			} else {
				$type = 'hex';
			}
		}
		
		if($type == 'hex') {
			return '#'.str_replace(' ', '', $this->color->hex());
		} elseif($type == 'rgb') {
			return 'rgb('.str_replace(' ', ', ', $this->color->rgb()).')';
		} elseif($type == 'hsb') {
			return str_replace(' ', ', ', $this->color->hsb());
		}
	}
	
	public function less($color, $percent = '100') {
		$percent = trim($percent, '% ');
		$percent /= 100;
		
		$color = $this->_map_color($color);
		$color = new Color($color);
		
		
		if($this->color->hsb('h') == 0) {
			if($color->hsb('h') > 180) {
				$h = 360;
			} else {
				$h = 0;
			}
		} else {
			$h = $this->color->hsb('h');
		}
		
		$c2_weight = $percent / 2;
		$c1_weight = 1 - $c2_weight;
		
		$hsb[0] = round($c1_weight * $h - $c2_weight * $color->hsb('h'));
		$hsb[1] = round($c1_weight * $this->color->hsb('s') - $c2_weight * $color->hsb('s'));
		$hsb[2] = round($c1_weight * $this->color->hsb('b') - $c2_weight * $color->hsb('b'));

		$rgb = implode(' ', $hsb);
								
		$this->color = $this->color->hsb($rgb);
		
		return $this;
	}
	
	public function more($color, $percent = 100) {
		$percent = trim($percent, '% ');
		$percent /= 100;

		// Dictionary lookup that maps 'yellow' to #ffff00 and 'indianred' to #cd5c5c
		$color = $this->_map_color($color);
		
		// Creates a new Color - this will automatically compute RGB, CYM, HEX, and HSB color models
		$color = new Color($color);
		
		// $this->color is current color , $color is the color to be added to original
		
		// Allows hue to be both 360 degrees and 0 degrees
		if($this->color->hsb('h') == 0) {
			if($color->hsb('h') > 180) {
				$h = 360;
			} else {
				$h = 0;
			}
		} else {
			$h = $this->color->hsb('h');
		}
		
		// Computes weights of colors - original:addedColor
		// 100% added means perfect blend 50:50
		// 50% means 75:25
		// 0% means no color added 100:0
		$c2_weight = $percent / 2;
		$c1_weight = 1 - $c2_weight;
		
		// Compute the hue, saturation, brightness values using the weights
		$hsb[0] = round($c1_weight * $h + $c2_weight * $color->hsb('h'));
		$hsb[1] = round($c1_weight * $this->color->hsb('s') + $c2_weight * $color->hsb('s'));
		$hsb[2] = round($c1_weight * $this->color->hsb('b') + $c2_weight * $color->hsb('b'));
		
		$hsb = implode(' ', $hsb);
			
		// Change current color into the new computed HSB value.	
		$this->color = $this->color->hsb($hsb);
		
		return $this;
	}

	public function reset() {
		$this->color = $this->cc;
		return $this;
	}
	
	public function lighter($percent = '20') {
		$percent = trim($percent, '% ');
		$hsb = explode(' ', $this->color->hsb());
		
		$brightness = $hsb[2];
		$brightness = ($brightness + $percent > 100) ? '100' : $brightness + $percent;
		$hsb[2] = $brightness;
		$hsb = implode(' ', $hsb);
				
		$this->color = $this->color->hsb($hsb);
		return $this;
	}
	
	public function darker($percent = '20') {
		$percent = trim($percent, '% ');
		$hsb = explode(' ', $this->color->hsb());
		
		$darkness = $hsb[2];
		$darkness = ($darkness - $percent < 0) ? '0' : $darkness - $percent;
		$hsb[2] = $darkness;
		$hsb = implode(' ', $hsb);
		
		$this->color = $this->color->hsb($hsb);
		return $this;
	}	
	
	public function addColor($name, $color) {
		try {
			$cc = new Color($color);
		} catch (Exception $e) {
			return;
		}
		
		$this->colors[$name] = $color;
	}
	
	public function _map_color($color) {
		if(isset($this->colors[$color])) {
			return $this->colors[$color];
		} else {
			return $color;
		}
	}

	private function _set_initial_colors() {
		return array(
			'aliceblue' => 'f0f8ff',
		    'antiquewhite' => 'faebd7',
	        'aqua' => '00ffff',
	        'aquamarine' => '7fffd4',
	        'azure' => 'f0ffff',
	        'beige' => 'f5f5dc',
	        'bisque' => 'ffe4c4',
	        'black' => '000000',
	        'blanchedalmond' => 'ffebcd',
	        'blue' => '0000ff',
	        'blueviolet' => '8a2be2',
	        'brown' => 'a52a2a',
	        'burlywood' => 'deb887',
	        'cadetblue' => '5f9ea0',
	        'chartreuse' => '7fff00',
	        'chocolate' => 'd2691e',
	        'coral' => 'ff7f50',
	        'cornflowerblue' => '6495ed',
	        'cornsilk' => 'fff8dc',
	        'crimson' => 'dc143c',
	        'cyan' => '00ffff',
	        'darkblue' => '00008b',
	        'darkcyan' => '008b8b',
	        'darkgoldenrod' => 'b8860b',
	        'darkgray' => 'a9a9a9',
	        'darkgreen' => '006400',
	        'darkkhaki' => 'bdb76b',
	        'darkmagenta' => '8b008b',
	        'darkolivegreen' => '556b2f',
	        'darkorange' => 'ff8c00',
	        'darkorchid' => '9932cc',
	        'darkred' => '8b0000',
	        'darksalmon' => 'e9967a',
	        'darkseagreen' => '8fbc8f',
	        'darkslateblue' => '483d8b',
	        'darkslategray' => '2f4f4f',
	        'darkturquoise' => '00ced1',
	        'darkviolet' => '9400d3',
	        'deeppink' => 'ff1493',
	        'deepskyblue' => '00bfff',
	        'dimgray' => '696969',
	        'dodgerblue' => '1e90ff',
	        'feldspar' => 'd19275',
	        'firebrick' => 'b22222',
	        'floralwhite' => 'fffaf0',
	        'forestgreen' => '228b22',
	        'fuchsia' => 'ff00ff',
	        'gainsboro' => 'dcdcdc',
	        'ghostwhite' => 'f8f8ff',
	        'gold' => 'ffd700',
	        'goldenrod' => 'daa520',
	        'gray' => '808080',
	        'green' => '008000',
	        'greenyellow' => 'adff2f',
	        'honeydew' => 'f0fff0',
	        'hotpink' => 'ff69b4',
	        'indianred' => 'cd5c5c',
	        'indigo' => '4b0082',
	        'ivory' => 'fffff0',
	        'khaki' => 'f0e68c',
	        'lavender' => 'e6e6fa',
	        'lavenderblush' => 'fff0f5',
	        'lawngreen' => '7cfc00',
	        'lemonchiffon' => 'fffacd',
	        'lightblue' => 'add8e6',
	        'lightcoral' => 'f08080',
	        'lightcyan' => 'e0ffff',
	        'lightgoldenrodyellow' => 'fafad2',
	        'lightgrey' => 'd3d3d3',
	        'lightgreen' => '90ee90',
	        'lightpink' => 'ffb6c1',
	        'lightsalmon' => 'ffa07a',
	        'lightseagreen' => '20b2aa',
	        'lightskyblue' => '87cefa',
	        'lightslateblue' => '8470ff',
	        'lightslategray' => '778899',
	        'lightsteelblue' => 'b0c4de',
	        'lightyellow' => 'ffffe0',
	        'lime' => '00ff00',
	        'limegreen' => '32cd32',
	        'linen' => 'faf0e6',
	        'magenta' => 'ff00ff',
	        'maroon' => '800000',
	        'mediumaquamarine' => '66cdaa',
	        'mediumblue' => '0000cd',
	        'mediumorchid' => 'ba55d3',
	        'mediumpurple' => '9370d8',
	        'mediumseagreen' => '3cb371',
	        'mediumslateblue' => '7b68ee',
	        'mediumspringgreen' => '00fa9a',
	        'mediumturquoise' => '48d1cc',
	        'mediumvioletred' => 'c71585',
	        'midnightblue' => '191970',
	        'mintcream' => 'f5fffa',
	        'mistyrose' => 'ffe4e1',
	        'moccasin' => 'ffe4b5',
	        'navajowhite' => 'ffdead',
	        'navy' => '000080',
	        'oldlace' => 'fdf5e6',
	        'olive' => '808000',
	        'olivedrab' => '6b8e23',
	        'orange' => 'ffa500',
	        'orangered' => 'ff4500',
	        'orchid' => 'da70d6',
	        'palegoldenrod' => 'eee8aa',
	        'palegreen' => '98fb98',
	        'paleturquoise' => 'afeeee',
	        'palevioletred' => 'd87093',
	        'papayawhip' => 'ffefd5',
	        'peachpuff' => 'ffdab9',
	        'peru' => 'cd853f',
	        'pink' => 'ffc0cb',
	        'plum' => 'dda0dd',
	        'powderblue' => 'b0e0e6',
	        'purple' => '800080',
	        'red' => 'ff0000',
	        'rosybrown' => 'bc8f8f',
	        'royalblue' => '4169e1',
	        'saddlebrown' => '8b4513',
	        'salmon' => 'fa8072',
	        'sandybrown' => 'f4a460',
	        'seagreen' => '2e8b57',
	        'seashell' => 'fff5ee',
	        'sienna' => 'a0522d',
	        'silver' => 'c0c0c0',
	        'skyblue' => '87ceeb',
	        'slateblue' => '6a5acd',
	        'slategray' => '708090',
	        'snow' => 'fffafa',
	        'springgreen' => '00ff7f',
	        'steelblue' => '4682b4',
	        'tan' => 'd2b48c',
	        'teal' => '008080',
	        'thistle' => 'd8bfd8',
	        'tomato' => 'ff6347',
	        'turquoise' => '40e0d0',
	        'violet' => 'ee82ee',
	        'violetred' => 'd02090',
	        'wheat' => 'f5deb3',
	        'white' => 'ffffff',
	        'whitesmoke' => 'f5f5f5',
	        'yellow' => 'ffff00',
	        'yellowgreen' => '9acd32'
		);
	}
}

$c1 = 'darkred';
$c2 = 'white';

$genius = S('color:genius', array($c1));
$genius->setup();

echo S('<div>')->height(300)->width(300)->style('background-color', $genius->color())->inner($genius->color('hsb'));

$genius->more($c2, 20);
echo S('<div>')->height(300)->width(300)->style('background-color', $genius->color())->inner($genius->color('hsb'));

$genius = S('color:genius', array($c2));
$genius->setup();

echo S('<div>')->height(300)->width(300)->style('background-color', $genius->color())->inner($genius->color('hsb'));


?>