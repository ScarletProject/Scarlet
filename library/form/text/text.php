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

	public function setup() {
		
		$this->defaults('label');
		$this->wrap('input', '/');
		
		$this->stylesheet('text.css')->script('jquery', 'text.js');
				
		if($this->arg('maxlength')) {
			$this->attr('maxlength', $this->arg('maxlength'));
		}
		
		$this->attr('value', $this->arg('label'));
		
		// Sets the default to be rounded with 6px
		if(!$this->arg('rounded')) {
			$this->addClass('rounded');
			$this->give('css:rounded/rounded.css', 'roundness', '7px');			
		}
	}
	
	public function show() {
		return '';
	}
}


?>