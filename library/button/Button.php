<?php

/** 
* Short Description
*
* Long Description
* @package Button
* @author Matt Mueller
*/

class Button extends Tag
{
	
	function init() {

		$this->defaults('value', 'click');

		// Generate a random color
		// mt_srand((double)microtime()*1000000);
		// 	    $c = '';
		// 	    while(strlen($c)<6){
		// 	        $c .= sprintf("%02X", mt_rand(0, 255));
		// 	    }		
		
		// $this->give('button.css', 'color', '#'.$c);
		$this->script('jquery');
		$this->stylesheet('button.css');
		$this->give('button.js', array('click' => $this->arg('click'), 'hover' => $this->arg('hover')));
		$this->give('css:rounded.css', 'roundness', '10px');
		
		$this->wrap('button');
		$this->addClass('rounded');
	}
	
	function tostring() {
		return $this->arg('value');
	}
}


?>