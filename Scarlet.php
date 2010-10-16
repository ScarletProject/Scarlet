<?php

// error_reporting(E_ALL);

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

function Scarlet($selection = null, $args = array(), $library = null) {
	return S($selection, $args, $library);
}

function S($selection = null, $args = array(), $library = null) {
	$S = new Scarlet;

	if(!isset($selection)) {
		return $S;
	}
	
	// Determine which way to route it.
	if(strstr($selection, '<') !== false) {
		preg_match('/<(?<open>[\w\s]+)(?<close>\/)?>([\w\s]+)?(<\/(?<close2>[\w\s]+)>)?/', $selection, $matches);
		
		if(isset($matches['open'])) {
			$open = $matches['open'];
		} else {
			throw new Exception("Unable to parse: $selection", 1);
		}
		
		if(isset($matches['close'])) {
			$close = $matches['close'];
		} elseif(isset($matches['close2'])) {
			$close = $matches['close2'];
		} else {
			$close = $open;
		}

		$tag = $S->_init_tag('HTMLElement', $args, $library);
		$tag->wrap($open, $close);
		
		return $tag;
	}
	elseif(strstr($selection, '.') !== false) {
		return $S->_init_template($selection);
	} else {
		return $S->_init_tag($selection, $args, $library);
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
	private static $stage;
	private static $assignments = array();
	
	private static $paths = array(
		'scarlet' => '',
		'scarlet_library' => '',
		'project' => '',
		'attachments' => '',
		'project_library' => '',
		'themes' => '',
		'template' => ''
	);

	public function parse($content = "") {
		if(!$content) return "";
		
		if(!class_exists('Template')) {
			require_once dirname(__FILE__).'/classes/Template.php';
		}
		
		$t = new Template;
		
		return $t->fetch($content);
	}

	public function assign($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return self::$assignments;
		} elseif(is_array($mixed)) {			
			foreach ($mixed as $key => $value) {					
				self::$assignments[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			self::$assignments[$mixed] = $value;
			return $this;
		} elseif(isset($assignments[$mixed])) {
			return self::$assignments[$mixed];
		} else {
			return '';
		}
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
		$namespace = explode(':',$asset);
		$filename = array_pop($namespace);
		$nsCount = count($namespace);
		$namespace = implode(':', $namespace);
		$loc = str_replace(':', '/', $namespace);
	
		foreach (self::$libraries as $lib) {
			$file = $lib.'/'.$loc;

			if(file_exists($file.'/'.$filename)) {
				$file = $file.'/'.$filename;
				return realpath($file);
			} elseif(file_exists($file.'/'.$loc.'/'.$filename)) {
				$file = $file.'/'.$loc.'/'.$filename;
				return realpath($file);
			} elseif(file_exists(dirname($file).'/'.$filename)) {
				$file = dirname($file).'/'.$filename;
				return realpath($file);
			}
		}
		
		return false;
		
	}
	
	public function find($namespace) {

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
	/*		
		init() - will default to init('.');
		init(...path...) - will create Scarlet in the appropriate path.
		
		Better to just create the directory if their isn't one, if there is one, just
		set things up and return Scarlet
	*/
	public function init($path = '.') {

		$path = realpath($path);

		if(basename($path) != 'Scarlet') {
			if(is_dir($path.'/Scarlet')) {
				$path .= '/Scarlet';
			} else {
				mkdir($path.'/Scarlet');
				$path = $path.'/Scarlet';
				// throw new Exception("Unable to locate your Scarlet Directory in: $path", 1);
			}
		}

		if(!is_dir($path)) {
			mkdir($path);
			// throw new Exception("Unable to locate your Scarlet Directory at: $path", 1);
		}

		// Make sure its not the main Scarlet directory
		if(file_exists($path.'/Scarlet.php')) {
			throw new Exception("Found Scarlet.php in Scarlet Directory, this is the main Scarlet Library, please include Scarlet directory that is in your project ", 1);
		}

		$S = S();
		// Define the specifics
		$S->path('project', $path);
		
		// Add attachments
		// exec('rm -r '.$S->path('project').'/attachments');
		if(!is_dir($S->path('project').'/attachments')) {
			mkdir($S->path('project').'/attachments');
		}
		$S->path('attachments', $S->path('project').'/attachments');
		
		// Add project library
		if(!is_dir($S->path('project').'/library')) {
			mkdir($S->path('project').'/library');
		}
		$S->path('project_library', $S->path('project').'/library');
		$S->library($S->path('project_library'));
		
		// Add themes
		if(!is_dir($S->path('project').'/themes')) {
			mkdir($S->path('project').'/themes');
		}
		$S->path('themes', $S->path('project').'/themes');
		
		// Compiled path
		$S->path('compiled', $S->path('project').'/compiled');
		
		
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
		
		return array_merge($scripts, $stylesheets);
	}
	
	public function copyAssets($assets = array(), $directory = null) {
		if(empty($assets) || $directory == null) {
			throw new Exception("Unable to copy assets - not all parameters satisfied", 1);
		}
		
		foreach ($assets as $asset => $mapped) {
			if(!file_exists($mapped) || strstr($mapped, '/attachments/') !== false) continue;
			
			$asset = str_replace(':','/',$asset);
			
			$file = $directory.'/'.$asset;
			if(file_exists($file)) {
				continue;
			}
			
			$path = dirname($file);
			$this->mkdir($path);
			
			copy($mapped, $file);
		}
	}

	public function _init_tag($namespace, $args = array(), $library = null) {
		if($namespace instanceof Tag) return $namespace;
		if(isset($initialized)) return $this->initialized;
		
		
		if(isset($library)) {
			$this->library($library);
		}
		

		$this->namespace = $namespace;

		// Load the class
		$this->register();
		
		if($namespace[0] == '/') {
			$namespace = explode(':', substr($namespace,1));
			$namespace[count($namespace)-1] = 'End'.$namespace[count($namespace)-1];
			$namespace = implode(':',$namespace);
		}
		
		$class = str_replace(':','_',$namespace);
		
		// Params to be sent to Tag
		$tagParams = array();
		$tagParams['namespace'] = $namespace;
		$tagParams['args'] = $args;
		
		// Creating the tag
		if(class_exists($class)) {
			$this->initialized = new $class($tagParams);
		} else {
			throw new Exception("Cannot find class: $class", 1);
		}

		// Definitely necessary(!!) - $this's got mixed up for some reason
		// Resulted in 3hr debug sesh... :'-(
		$this->unregister();		

		// Incase namespace changed because its an end tag
		$this->namespace = $namespace;

		return $this->initialized;
	}
	
	public function _init_template($template) {
		if(isset($this->initialized)) return $this->initialized;
				
		$suffix = end(explode('.', $template));
		
		if($suffix == 'tpl' || $suffix == 'php' || $suffix == 'html') {
			if(!class_exists('Template')) {
				require_once dirname(__FILE__).'/classes/Template.php';
			}
			$this->initialized = new Template($template);			
		} else {
			throw new Exception("Unable to initialize template: $template", 1);
		}
		
		return $this->initialized;
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
	
	public function stage($stage = null) {
		if(!isset(self::$stage)) {
			if(isset($stage)) {				
				self::$stage = $stage;
				return $this;
			} else {
				return false;
			}
		} elseif(!isset($stage)) {
			return self::$stage;
		} else {
			// echo $stage;echo "<br/>";echo $this->stage;
			if(strcasecmp($stage , self::$stage) == 0) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function mkdir($path) {
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

// Define scarlet paths
S()->path('scarlet', realpath(dirname(__FILE__)));
S()->path('scarlet_library', S()->path('scarlet').'/library');

// Load the default library
S()->library(S()->path('scarlet_library'));

?>