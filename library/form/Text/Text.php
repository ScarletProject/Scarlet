<?php

/** 
* Short Description
*
* Long Description
* @package Form_Text
* @author Matt Mueller
*/

class Form_Text extends Tag
{
	// public $value;
	// public $width;
	// public $maxlength;
	// private $Tag;
	
	public function init() {
		
		$this->defaults('value', 'maxlength');
		
		// $this->value = $this->arg('value');
		// $this->width = $this->arg('width');
		// $this->maxlength = $this->arg('maxlength');
		
		$this->stylesheet('text.css')->script('jquery', 'text.js');
		
		$this->addClass('form', 'text');
		
		// Sets the default to be rounded with 6px
		if(!$this->arg('rounded')) {
			$this->addClass('rounded');
			$this->give('css:rounded.css', 'roundness', '6px');
		}
	}
	
	public function tostring() {
		$out = '<input type = "text" 
			value = "'.$this->arg('value').'" 
			style="width:'.$this->arg('width').';"
			maxlength="'.$this->arg('maxlength').'"
		/>';
		
		return $out;
	}
}


?>