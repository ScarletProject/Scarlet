<?php

/** 
* Short Description
*
* Long Description
* @package Menu_Vertical extends Tag
* @author Matt Mueller
*/

class Menu_Vertical extends Tag 
{
	
	public function setup()
	{
		$this->wrap(true, false);
		$this->stylesheet('vertical.css');
	}
	
	public function show()
	{
		return S('<ul>')->wrap(true, false);
	}
}

class Menu_EndVertical extends Tag 
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