<?php

/** 
* Short Description
*
* Long Description
* @package Form
* @author Matt Mueller
*/

class Form extends Tag
{
	public function setup() {

		$this->defaults('action', 'method = post');
		$this->stylesheet('form.css');
		$this->attr(array(
			'action' => $this->arg('action'),
			'method' => $this->arg('method')
		));
		
		$this->wrap('form', false);
		
	}
		
	public function show() {
		return '';
	}
}

/** 
* Short Description
*
* Long Description
* @package EndForm
* @author Matt Mueller
*/

class EndForm extends Tag
{
	
	function setup() {
		$this->wrap(false, 'form');
	}
	
	public function show() {
		return '';
	}
}


?>