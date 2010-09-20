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

	public function init() {
		
		$this->defaults('value', 'maxlength');
				
		$this->stylesheet('text.css')->script('jquery', 'text.js');
		
		$this->addClass('form', 'text');
		
		// Sets the default to be rounded with 6px
		if(!$this->arg('rounded')) {
			$this->addClass('rounded');
			$this->give('css:rounded/rounded.css', 'roundness', '7px');			
		}
	}
	
	public function tostring() {
		$out = S('<input />')->attr(array(
			'value' => $this->arg('value'),
			'maxlength' => $this->arg('maxlength')
		))->width($this->arg('width'));

		return $out;
	}
}


?>