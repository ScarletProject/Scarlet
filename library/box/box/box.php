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
		
		$this->addClass('box');
		$this->stylesheet('box.css');
		$this->wrap('div', false);
		
		if($this->arg('width')) {
			$width = str_replace('px', '', $this->arg('width'));
			$this->width($width - 14);
		}
		
		if($this->arg('height')) {
			$this->height($this->args('height'));
		}
		
		if($this->args('rounded')) {
			$this->addClass('rounded');
			$this->give('css:rounded.css', 'roundness', $this->args('rounded'));
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