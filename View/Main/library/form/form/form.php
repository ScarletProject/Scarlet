<?php

/** 
* Short Description
*
* Long Description
* @package Form
* @author Matt Mueller
*/

class Form 
{
	
	function init()
	{
		$this->extend('box');
		$this->defaults('title');
		
		$this->wrap('form', false);
	}
	
	function tostring() {
		if($this->args('title'))
	}
}

/** 
* Short Description
*
* Long Description
* @package End_Form
* @author Matt Mueller
*/

class End_Form 
{
	
	function __construct(argument)
	{
		$this->extend('box');
		$this->wrap(false, 'form');
	}
	
	function tostring() {
		
	}
}


?>