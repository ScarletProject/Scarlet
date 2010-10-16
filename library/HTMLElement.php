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
	
	public function setup() {
		// $this->removeAttr('class');
		$this->removeClass('scarlet-htmlelement');
	}
	
	public function show() {
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