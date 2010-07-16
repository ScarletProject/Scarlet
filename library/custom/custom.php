<?php

/** 
* Short Description
*
* Long Description
* @package Tag
* @author Matt Mueller
*/

class Custom extends Tag
{
	
	function init() {				
		$this->wrap(false);

		foreach ($this->args() as $key => $arg) {
			if(!is_numeric($key)) continue;

			if(defined('SCARLET_PROJECT_LIBRARY_DIR')) {
				$loaded_path = $this->_map($arg);
				$tag_path = str_replace(':', '/', $arg);
				$dirs = explode('/', dirname($tag_path));
				
				if(file_exists(SCARLET_PROJECT_LIBRARY_DIR.'/'.$tag_path)) {
					continue;
				}

				$path = '';
				foreach ($dirs as $dir) {
					if($dir == '')
						continue;
					$path .= $dir;
					if(!is_dir(SCARLET_PROJECT_LIBRARY_DIR.'/'.$path)) {
						mkdir(SCARLET_PROJECT_LIBRARY_DIR.'/'.$path);
					}
					$path .= '/';
				}
				
				$path = SCARLET_PROJECT_LIBRARY_DIR.'/'.$path;
				$contents = file_get_contents($loaded_path);
				file_put_contents($path.basename($tag_path), $contents);
			}
		}
	}
	
	function tostring()
	{
		return '';
	}
}

class EndCustom extends Tag {
	
	function init() {
		$this->wrap(false);
		$this->defaults('name');
		
		if($this->arg('name')) {
			
			
			
		}
	}
	
	function tostring() {
		return '';
	}
}


?>