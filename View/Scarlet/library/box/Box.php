<?php

/** 
* Short Description
*
* Long Description
* @package Box
* @author Matt Mueller
*/

class Box extends Tag
{
	private $header;
	private $body;

	function init() {
		$this->defaults('header');
		$this->stylesheet('box.css');
		$this->wrap('div', false);
		$this->addClass('box');
		
		$this->header = S('<div>')->addClass('header')->inner($this->arg('header'));
		$this->body = S('<div>')->addClass('body')->wrap('div', false)->height($this->arg('height')-48);
		
		
		if($this->arg('rounded')) {
			$this->addClass('rounded');
			$this->header->addClass('rounded top');
			$this->body->addClass('rounded bottom');
		}
	}
	
	function tostring() {
		$out = $this->header.$this->body;
		
		return $out;
	}
}

class EndBox extends Tag
{
	function init() {
		$this->wrap(false, 'div');
	}
	
	function tostring() {
		return '</div>';
	}
}


?>