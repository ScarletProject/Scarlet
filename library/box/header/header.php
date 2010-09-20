<?php

/** 
* Short Description
*
* Long Description
* @package Header
* @author Matt Mueller
*/

class Box_Header extends Tag
{
	
	function init()
	{
		$this->wrap('div');
		$this->defaults('header = Text goes here')
		
		$box = S('box')->args($this->args());
		
		$this->before($box);
		
		$this->stylesheet('header.css');
		$this->addClass('header');
		
		// if($this->args('rounded')) {
		// 	$this->addClass('rounded top');
		// }
		
	}
	
	function tostring()
	{
		return $this->arg('header');
	}
}

/** 
* Short Description
*
* Long Description
* @package Box_EndHeader
* @author Matt Mueller
*/

class Box_EndHeader extends Tag
{
	
	function init()
	{
		$this->wrap(false);
		
		$endbox = S('/box');
		$this->after($endbox);
	}
	
	function tostring()
	{
		return '';
	}
}



?>