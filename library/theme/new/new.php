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
		
		if(!defined('SCARLET_PROJECT_THEME_DIR')) {
			return;
		}
		
		$this->defaults('name');

		// Make theme'd directory
		if(!is_dir(SCARLET_PROJECT_THEME_DIR.'/'.$this->arg('name'))) {
			mkdir( SCARLET_PROJECT_THEME_DIR.'/'.$this->arg('name') );
		}
		
		$directory = SCARLET_PROJECT_THEME_DIR.'/'.$this->arg('name');

		$scripts_before = $this->script();
		$stylesheets_before = $this->stylesheet();
		$attachments_before = $this->attach();

		foreach ($this->args() as $key => $tag) {
			if(!is_numeric($key)) continue;
			
			$this->_clear_scripts();
			$this->_clear_stylesheets();
			$this->_clear_attachments();

			S($tag)->__tostring();
			
			// Get files in
			$scripts = $this->script();
			$stylesheets = $this->stylesheet();
			$attachments = $this->attach();
			
			// echo Util::build($tag, $directory);
						
			$assets = Util::getAssets($tag);
						
			foreach ($assets['scripts'] as $script => $mapping) {
				if(strstr($script, '.') === false) {
					// Deal with me later
				} elseif(strstr($script, 'Scarlet/attachments') !== false) {
					// Not sure if this should be handled or not.
				} else {
					Util::place($script, $directory, $mapping);
				}				
			}
			
			foreach ($assets['stylesheets'] as $stylesheet => $mapping) {
				if(strstr($stylesheet, '.') === false) {
					// Deal with me later
				} elseif(strstr($stylesheet, 'Scarlet/attachments') !== false) {
					// Not sure if this should be handled or not.
				}
				else {
					Util::place($stylesheet, $directory, $mapping);
				}				
			}

		}
	}
	
	function tostring()
	{
		return '';
	}
}


?>