<?php

/** 
* Short Description
*
* Long Description
* @package Menu_Horizontal extends Tag
* @author Matt Mueller
*/

class Menu_Horizontal extends Tag 
{
	
	public function setup()
	{
		$this->wrap(true, false);
		$this->stylesheet('horizontal.css');
	}
	
	public function show()
	{
		return S('<ul>')->wrap(true, false);
	}
}

class Menu_EndHorizontal extends Tag 
{
	
	public function setup()
	{
		$this->wrap(false, true);
	}
	
	public function show()
	{
		return S('<ul>')->wrap(false, true);
	}
}


?>