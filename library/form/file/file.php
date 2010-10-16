<?php

/** 
* Short Description
*
* Long Description
* @package form_file extends Tag
* @author Matt Mueller
*/

class form_file extends Tag 
{
	private $input;
	private $fake;
	private $button;
	
	function setup()
	{
		$this->defaults('value = Upload');
		$this->script('jquery', 'file.js');
		$this->stylesheet('file.css');
		
		$this->fake = S('form:text', array('Choose File'))->addClass('scarlet-file-input')->removeScript('form:text:text.js');
		$this->button = S('button', array($this->arg('value')))->arg('rounded', '6px');
	}
	
	function show()
	{
		return $this->fake.$this->button;
	}
}


?>