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
	
	public function init()
	{
		$this->wrap(true, false);
		$this->stylesheet('vertical.css');
	}
	
	public function tostring()
	{
		return S('<ul>')->wrap(true, false);
	}
}

class Menu_EndVertical extends Tag 
{
	
	public function init()
	{
		$this->wrap(false, true);
	}
	
	public function tostring()
	{
		return S('<ul>')->wrap(false, true);
	}
}


?>