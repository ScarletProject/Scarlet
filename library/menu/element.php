<?php

/** 
* Short Description
*
* Long Description
* @package Menu_Element extends Tag
* @author Matt Mueller
*/

class Menu_Element extends Tag 
{
	
	function setup()
	{
		$this->defaults('title', 'link');
		$this->wrap('li');
	}
	
	function show()
	{
		return S('<a>')->attr('href', $this->arg('link'))->inner($this->arg('title'));
	}
}


?>