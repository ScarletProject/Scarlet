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
		$this->script('cycle');
		$this->width($this->args('width'));
		$this->height($this->args('height'));
		$this->style('background-color', $this->args('background-color'));
		$this->style('border', $this->args('border'));
		
		if($this->args('rounded')) {
			$this->stylesheet('box:rounded:rounded.css');
		}
	}
	
	function tostring() {
		return '';
	}
}


?>