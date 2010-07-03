<?php

/** 
* Short Description
*
* Long Description
* @package Round
* @author Matt Mueller
*/

class Box_Rounded extends Tag
{
	
	function init()
	{
		$this->extend('box');
		
		$this->defaults('rounded');
		$this->addClass('rounded');
		
		$this->give('rounded.css', 'roundness', $this->args('rounded'));
	}
	
	function tostring() {
		'';
	}
}


?>