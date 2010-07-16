<?php

/** 
* Short Description
*
* Long Description
* @package Util
* @author Matt Mueller
*/

class Util 
{
	public static function getAssets($namespace) {
		$tag = S($namespace);
		
		$scripts_before = $tag->script();
		$stylesheets_before = $tag->stylesheet();
		
		// Take the scripts out
		$tag->_clear_scripts();
		$tag->_clear_stylesheets();
		
		
		// Run the tag
		$tag->__tostring();
		
		// Get files in
		$scripts = $tag->script();
		$stylesheets = $tag->stylesheet();
		
		$js = S('javascript');		
		foreach ($scripts as $i => $script) {
			$scripts[$i] = $js->map($script);
		}
		
		$css = S('css');
		foreach ($stylesheets as $i => $sheet) {
			$stylesheets[$i] = $css->map($sheet);
		}
		
		// Take the scripts out
		$tag->_clear_scripts();
		$tag->_clear_stylesheets();

		// Put the scripts back.
		foreach ($scripts_before as $script) {
			$tag->script($script);
		}
		
		foreach ($stylesheets_before as $stylesheet) {
			$tag->stylesheet($stylesheet);
		}
		
		return array('scripts' => $scripts, 'stylesheets' => $stylesheets);
		
	}
	
	public static function place($asset, $directory, $source_file) {
		$path = str_replace(':', '/', $asset);
		$path = explode('/', $path);
		$filename = array_pop($path);
		$path = implode('/', $path);

		if(!file_exists($directory.'/'.$path.'/'.$filename)) {
			Util::mkdir($path, $directory);
			copy($source_file, $directory.'/'.$path.'/'.$filename);
		}
		
		return $path.'/'.$filename;
	}
	
	public static function find($asset) {
		
	}
	
	public static function build($namespace, $directory) {
		// Make tag directory structure
		$tag_path = str_replace(':', '/', $namespace);
		
		Util::mkdir($tag_path, $directory);
	}
	

	
	public static function findNamespace($namespace)
	{
		
	}
	
	public static function findAsset($asset)
	{
		
	}
	
	// Might not be necessary.
	public static function copyAsset($asset, $destination, $source)
	{
		
	}
	
	public static function mkdir($path, $directory) {
		$dirs = explode('/', $path);
		
		if(!is_dir($path)) {
			$path_part = '';
			foreach ($dirs as $dir) {
				if($dir == '')
					continue;
				$path_part .= $dir;
				if(!is_dir($directory.'/'.$path_part)) {
					mkdir($directory.'/'.$path_part);
				}
				$path_part .= '/';
			}
		}
		return $directory.'/'.$path;
	}
	
}


?>