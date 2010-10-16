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
	
	function setup() {
		
		$this->defaults('type');
				
		$this->wrap(false);
	}
	
	function show()
	{
		$out = '';
		switch ($this->arg('type')) {
			case 'strict':
				$out = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
					"http://www.w3.org/TR/html4/strict.dtd">';
				break;
			case 'transitional':
				$out = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
					"http://www.w3.org/TR/html4/loose.dtd">';
				break;
			default:
				$out = '<!DOCTYPE html>';
				break;
		}
		
		return $out;
	}
}


?>