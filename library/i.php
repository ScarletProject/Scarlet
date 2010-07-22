<?php

/** 
* Include Tag
*
* Long Description
* @package i
* @author Matt Mueller
*/

class i extends Tag
{
	
	function init()
	{
		$args = $this->defaults('template');
		$this->wrap(false);
	}
	
	function tostring() {
		return S($this->arg('template'))->fetch();
	}
}


?>