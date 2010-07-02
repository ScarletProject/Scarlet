<?php

/** 
* Short Description
*
* Long Description
* @package Round
* @author Matt Mueller
*/

class Box_Round extends Tag
{
	
	function init()
	{
		$this->defaults('rounded');
		$this->useDefaults('box');
		
		print_r($this->args());
		
		$round = $this->args('rounded');
		
		@ob_clean();
		ob_start();
		include_once('rounded.custom.css.php');
		$contents = ob_get_contents();
		ob_end_clean();
		
		file_put_contents(dirname(__FILE__).'/rounded.css',$contents);
		
		$this->addClass('rounded');
		$this->stylesheet('rounded.css');
		$this->wrap(false);
	}
	
	function tostring() {
		$this->removeArg('rounded');
		
		return S('box')->args($this->args());
	}
}


?>