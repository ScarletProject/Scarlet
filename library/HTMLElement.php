<?php

/** 
* Short Description
*
* Long Description
* @package HTMLElement extends Tag
* @author Matt Mueller
*/

class HTMLElement extends Tag 
{
	private $inner = '';
	
	public function init() {
		// Silence is POWER.
	}
	
	public function tostring() {
		return $this->inner;
	}
	
	public function inner($html = null) {
		if(!isset($html)) {
			return $this->inner;
		} else {
			$this->inner = $html;
			return $this;
		}
	}
	
}



?>