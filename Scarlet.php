<?php

// Require Tag
if(!class_exists('Tag')) {
	require_once dirname(__FILE__).'/classes/Tag.php';
}

/** 
* Short Description
*
* Long Description
* @package Scarlet
* @author Matt Mueller
*/

function S($ns_or_tpl = null, $args = array(), $library = null) {
	$S = new Scarlet;

	if(!isset($ns_or_tpl)) {
		return $S;
	}
	
	// Determine which way to route it.
	if(strstr($ns_or_tpl, '.') !== false) {
		return $S->initTemplate($ns_or_tpl);
	} else {
		return $S->initTag($ns_or_tpl, $args, $library);
	}
}

//////////////////////////////////////////
///            SCARLET CLASS           ///
/// SHOULD NOT BE INSTATIATED DIRECTLY ///
///         USE S(...) INSTEAD         ///
//////////////////////////////////////////

class Scarlet
{
	private $namespace;
	private static $libraries = array();
	private $initialized;
	
	private static $paths = array(
		'scarlet' => '',
		'scarlet_library' => '',
		'project' => '',
		'attachments' => '',
		'project_library' => '',
		'themes' => '',
		'template' => ''
	);

	public function initTag($namespace, $args = array(), $library = null) {
		if($namespace instanceof Tag) return $namespace;
		if(isset($initialized)) return $this->initialized;
		
		
		if(isset($library)) {
			$this->library($library);
		}
		
		if($namespace[0] == '/') {
			$namespace = explode(':', substr($namespace,1));
			$namespace[count($namespace)-1] = 'End'.$namespace[count($namespace)-1];
			$namespace = implode(':',$namespace);
		}
		$this->namespace = $namespace;
		
		// Load the class
		$this->register();
		
		$class = str_replace(':','_',$namespace);
		
		// Params to be sent to Tag
		$tagParams = array();
		$tagParams['namespace'] = $namespace;
		$tagParams['args'] = $args;
		
		// Creating the tag
		$this->initialized = new $class($tagParams);

		// Definitely necessary(!!) - $this's got mixed up for some reason
		// Resulted in 3hr debug sesh... :'-(
		$this->unregister();		

		return $this->initialized;
	}
	
	public function initTemplate($template) {
		if(isset($this->initialized)) return $this->initialized;
				
		$suffix = end(explode('.', $template));
		
		if($suffix == 'tpl' || $suffix == 'php') {
			if(!class_exists('Template')) {
				require_once dirname(__FILE__).'/classes/Template.php';
			}
			$this->initialized = new Template($template);			
		} else {
			throw new Exception("Unable to initialize template: $template", 1);
		}
		
		return $this->initialized;
	}
	
	public function path($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return self::$paths;
		} elseif(is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				self::$paths[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			self::$paths[$mixed] = $value;
			return $this;
		} elseif(isset(self::$paths[$mixed])) {
			return self::$paths[$mixed];
		} else {
			return false;
		}
	}
	
	public function findAsset($asset) {
		
		$as = explode(':',$asset);
		$filename = end($as);
		$asloc = str_replace(':','/',$asset);

		foreach (self::$libraries as $lib) {
			$file = $lib.'/'.$asloc;
			
			if(file_exists($file)) {
				return $file;
			}
		}
		
		return false;
		
	}
	
	public function find($namespace = null) {
		if(!isset($namespace)) {
			throw new Exception("No namespace given for find!", 1);
		}
		
		$ns = explode(':',$namespace);
		$class = end($ns);
		$nsloc = str_replace(':','/',$namespace);
		
		foreach (self::$libraries as $lib) {

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
	
	public function library() {
		$paths = func_get_args();

		if(empty($paths)) {
			return self::$libraries;
		} elseif(count($paths) == 1 && is_array($paths[0])) {
			$paths = $paths[0];
		}

		foreach ($paths as $path) {
			if(is_dir(realpath($path))) {
				array_unshift(self::$libraries, realpath($path));
			}
			else {
				throw new Exception("Could not find Library: ".$path, 1);
			}
		}
		
		// Uniquify the libraries - prevents a build-up of default library
		self::$libraries = array_unique(self::$libraries);

		return $this;
	}
	
	public function removeLibrary() {
		$libraries = func_get_args();
		if(count($libraries == 1) && is_array($libraries[0])) {
			$libraries = $libraries[0];
		}
		
		$library_arr = self::$libraries;
		$library_count = count(self::$libraries);

		foreach ($libraries as $library) {
			if(is_numeric($library)) {
				if($library_count > $library && $library >= 0) {
					for($i = 0; $i < $library; $i++) {
						next($library_arr);
					}

					unset(self::$libraries[key($library_arr)]);
				}
			} else {
				$index = array_search(realpath($library), self::$libraries);
				if($index !== false) {
					unset(self::$libraries[$index]);
				}
			}
		}
		return $this;
	}
	
	public function projectPath($path = null) {
		if(!isset($path)) {
			return S()->path('project');
		}

		$path = realpath($path);

		if(basename($path) != 'Scarlet') {
			if(is_dir($path.'/Scarlet')) {
				$path .= '/Scarlet';
			} else {
				throw new Exception("Unable to locate your Scarlet Directory in: $path", 1);
			}
		}

		if(!is_dir($path)) {
			throw new Exception("Unable to locate your Scarlet Directory at: $path", 1);
		}

		// Make sure its not the main Scarlet directory
		if(file_exists($path.'/Scarlet.php')) {
			throw new Exception("Found Scarlet.php in Scarlet Directory, this is the main Scarlet Library, please include Scarlet directory thats in your project ", 1);
		}

		$S = S();
		// Define the specifics
		$S->path('project', $path);
		$S->path('attachments', $S->path('project').'/attachments');
		$S->path('project_library', $S->path('project').'/library');
		// Add the project library
		$S->library($S->path('project_library'));
		
		$S->path('themes', $S->path('project').'/themes');
		
		return $this;
	}
	
	public function location($namespace = null) {
		return dirname($this->find($namespace));
	}
	
	public function getAssets($namespace = null, $args = array()) {
		if(!isset($namespace)) {
			return array();
		}
		
		$tag = S($namespace)->arg($args);
		$tag->__tostring();
		$scripts = $tag->_used_scripts();
		$stylesheets = $tag->_used_stylesheets();
		
		return array('scripts' => $scripts, 'stylesheets' => $stylesheets);
	}
		
	private function register() {
		spl_autoload_register(array($this, 'loadClass'));
		return $this;
	}
	
	private function unregister() {
		spl_autoload_unregister(array($this, 'loadClass'));
		return $this;
	}
	
	private function loadClass() {
		$file = S()->find($this->namespace);
	
		if(!$file) {
			throw new Exception("Cannot load namespace: $this->namespace", 1);
		} else {
			require_once($file);
		}
	}
	
	private function mkdir($path) {
		$path = trim($path);
		$path = rtrim($path, '/');
		$dirs = explode('/', $path);
		if(is_dir($path)) {
			return $path;
		}
		
		$path_part = '';
		foreach ($dirs as $dir) {
			if($dir == '') {
				$path_part .= '/';
				continue;
			}
			$path_part .= $dir;
			if(!is_dir($path_part)) {
				mkdir($path_part);
			}
			
			$path_part .= '/';
		}
			
		return $path;
	}
}

$S = S();

// Define scarlet paths
$S->path('scarlet', realpath(dirname(__FILE__)));
$S->path('scarlet_library', $S->path('scarlet').'/library');

// Load the default library
$S->library($S->path('scarlet_library'));

?>