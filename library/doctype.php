<?php

/** 
* Short Description
*
* Long Description
* @package Doctype
* @author Matt Mueller
*/

class Doctype extends Tag
{
	
	function init() {
		
		$this->defaults('type');
				
		$this->wrap(false);
	}
	
	function tostring()
	{
		return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
			"http://www.w3.org/TR/html4/loose.dtd">';
	}
}


?>