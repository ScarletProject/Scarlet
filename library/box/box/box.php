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
		$this->defaults('width', 'height', 'color', 'border');

		$this->width($this->args('width'));
		$this->height($this->args('height'));
		$this->style('background-color', $this->args('color'));
		$this->style('border', $this->args('border'));
		
		// Implement later if necessary...
		// if($this->args('rounded')) {
		// 	$this->become('box:rounded');
		// }
	}
	
	function tostring() {
		return '';
	}
}


?>