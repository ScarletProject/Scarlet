<?php

/** 
* Short Description
*
* Long Description
* @package Theme_Default
* @author Matt Mueller
*/

class Theme_Default extends Tag
{
	
	public function setup()
	{
		$this->defaults('name');
		$directory = S()->path('themes').'/'.$this->arg('name');
		
		if(is_dir($directory)) {
			S()->library($directory);
		}
	}
	
	public function show()
	{
		return '';
	}
}

class Theme_EndDefault extends Tag
{
	
	public function setup()
	{
		$this->defaults('name');
		$directory = S()->path('themes').'/'.$this->arg('name');
		
		if(is_dir($directory)) {
			S()->removeLibrary($directory);
		}
	}
	
	public function show()
	{
		return '';
	}
}

?>