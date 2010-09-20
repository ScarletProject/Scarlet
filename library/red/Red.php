<?php

/** 
* Short Description
*
* Long Description
* @package Red extends Tag
* @author Matt Mueller
*/

class Red extends Tag 
{
	
	function init()
	{
		$this->wrap('div', false);
		$this->style('color', 'red');
		
		
		$this->stylesheet('style.css');
	}
	
	function tostring() {
		return '';
	}
}

class EndRed extends Tag {
	
	function init()
	 {
		$this->wrap(false, 'div');
	}
	
	function tostring() {
		return '';
	}
}

?>