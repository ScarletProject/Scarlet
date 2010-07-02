<?php
/** 
* Short Description
*
* Long Description
* @package Img
* @author Matt Mueller
*/

class Img extends Tag
{

	public function init() {
		// Tag::__construct($init);
		$this->defaults('src', 'caption');
		
		$this->attr('src', $this->args('src'));
		$this->attr('alt', $this->args('caption'));
		
		$this->wrap('img', '/');
	}
	
	public function tostring() {
		return '';
	}
	
	
	
}


?>