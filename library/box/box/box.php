<?php

/** 
* Short Description
*
* Long Description
* @package Box extends Tag
* @author Matt Mueller
*/

class Box extends Tag 
{
	
	function init()
	{
		$this->defaults('width', 'height');
		$this->stylesheet('box.css');
		$this->wrap('div', false);
		
		if($this->arg('width')) {
			$width = str_replace('px', '', $this->arg('width'));
			$this->width($width - 14);
		}
		
		if($this->arg('height')) {
			$this->height($this->args('height'));
		}
		
		// Handle rounded arguments
		if($this->args('rounded')) {
			$this->give('css:rounded/rounded.css', 'roundness', $this->args('rounded'));
		}
		
		if($this->args('rounded-bottom')) {
			$this->give('css:rounded/roundedBottom.css', 'roundness', $this->args('rounded-bottom'));
		}
		
		if($this->args('rounded-top')) {
			$this->give('css:rounded/roundedTop.css', 'roundness', $this->args('rounded-top'));
		}
		
		if($this->args('rounded-left')) {
			$this->give('css:rounded/roundedLeft.css', 'roundness', $this->args('rounded-left'));
		}
		
		if($this->args('rounded-right')) {
			$this->give('css:rounded/roundedRight.css', 'roundness', $this->args('rounded-right'));
		}
		
	}
	
	function tostring() {
		$out = '';
		// if ($this->args('header')) {
		// 	$out = '<div class = "header ';
		// 
		// 	if($this->args('rounded')) {
		// 		$out .= 'rounded top';
		// 	}
		// 
		// 	$out .= '">'.$this->args('header').'</div>';
		// }
		
		return $out;
	}
}

/** 
* Short Description
*
* Long Description
* @package EndBox extends Tag
* @author Matt Mueller
*/

class EndBox extends Tag 
{
	function init() {
		$this->wrap(false, 'div');
	}
	
	function tostring() {
		return '';
	}
}



?>