<?php

/** 
* Sets up the autoloading
*
* Long Description
* @package ClassLoader
* @author Matt Mueller
*/

class ClassLoader 
{
	private $libraries = array();
	private $namespaces = array();
	
	public function library() {
		$paths = func_get_args();

		if(empty($paths)) {
			return $this->libraries;
		}

		foreach ($paths as $path) {
			if(is_dir(realpath($path))) {
				$this->libraries[] = realpath($path);
				$this->libraries = array_unique($this->libraries);
			}
			else {
				throw new Exception("Could not find Library: ".$path, 1);
			}
		}

		return $this;
	}
 	
	public function add() {
		$namespaces = func_get_args();

		if(empty($namespaces)) {
			return $this->namespaces;
		}

		foreach ($namespaces as $namespace) {
			$this->namespaces[] = $namespace;
		}

		return $this;
	}

	public function register() {
		spl_autoload_register(array($this, 'loadClass'));
	}
	
	private function loadClass() {
		foreach ($this->namespaces as $namespace) {
			$location = $this->find($namespace);
		
			if(!$location) {
				throw new Exception("Cannot load namespace: $namespace", 1);
			} else {
				require_once($location);
			}
		}
		
		
	}

	public function find($namespace) {		
		foreach ($this->libraries as $lib) {
			$ns = explode(':',$namespace);
			$class = end($ns);
			$nsloc = str_replace(':','/',$namespace);

			$file = $lib.'/'.$nsloc;

			// Most basic, just lying in library as php file.
			if(file_exists($file.'.php')) {
				$file .= '.php';
				return $file;
			}
			// Has its own folder
			elseif(file_exists($file.'/'.$class.'.php')) {
				$file = $file.'/'.$class.'.php';				
				return $file;
			}
			// Handles if default has its own folder
			elseif(count($ns) == 1 && file_exists($file.'/'.$class.'/'.$class.'.php')) {
				$file = $file.'/'.$class.'/'.$class.'.php';
				return $file;
			}
		}
		
		return false;
	}
	
	public function location($namespace) {
		$location = $this->find($namespace);
		return dirname($location);
	}
}

?>