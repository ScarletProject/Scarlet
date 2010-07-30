<?php

/** 
* Short Description
*
* Long Description
* @package Theme_New
* @author Matt Mueller
*/

class Theme_New extends Tag
{
	
	function init()
	{		
		$this->wrap('false');
		
		if(!S()->path('themes')) {
			return;
		}
		
		$this->defaults('name');

		// Make theme'd directory
		if(!is_dir(S()->path('themes').'/'.$this->arg('name'))) {
			mkdir( S()->path('themes').'/'.$this->arg('name') );
		}
		
		$directory = S()->path('themes').'/'.$this->arg('name');


		foreach ($this->args() as $key => $tag) {
			if(!is_numeric($key)) continue;
			
			$assets = S()->getAssets($tag);
			
			S()->copyAssets($assets, $directory);
		}
		
		if($this->arg('default')) {
			S('theme:default')->arg('name', $this->arg('name'))->__tostring();
		}
	}
	
	function tostring()
	{
		return '';
	}
}

class Theme_EndNew extends Tag 
{
	function init()
	{
		$this->wrap('false');
		
		if(!S()->path('themes')) {
			return;
		}
		
		foreach ($this->args() as $name) {
			if(is_dir(S()->path('themes').'/'.$name)) {
				$this->rmdir(S()->path('themes').'/'.$name);
			}
		}
	}
	
	function tostring()
	{
		return '';
	}
	
	private function rmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (filetype($dir."/".$object) == "dir") $this->rmdir($dir."/".$object); else unlink($dir."/".$object); 
	       } 
	     } 
	     reset($objects); 
	     rmdir($dir); 
	   } 
	 }
}


?>