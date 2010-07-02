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
		$this->defaults('width', 'height', 'background', 'border');
		
		$this->width($this->args('width'));
		$this->height($this->args('height'));
		$this->style('background', $this->args('background'));
		$this->style('border', $this->args('border'));
	}
	
	function tostring() {
		if($this->args('rounded')) {
			$this->wrap(false);
			return S('box:round')->args($this->args());
		} else {
			$this->wrap(true);
		}
		
		return '';
	}
}


?>