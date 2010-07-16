<?php

/** 
* Short Description
*
* Long Description
* @package Div extends Tag
* @author Matt Mueller
*/

class Div extends Tag 
{
	
	function init()
	{
		$this->wrap('div', false);
	}
	
	function tostring() {
		return '';
	}
}

class Div extends Tag 
{
	
	function init()
	{
		$this->wrap(false, 'div');
	}
	
	function tostring() 
	{
		return '';
	}
}

?>