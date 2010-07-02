<?php

/** 
* Short Description
*
* Long Description
* @package Tag
* @author Matt Mueller
*/
// include_once dirname(__FILE__).'/../interfaces/TagInterface.php';

class Tag
{
	private
		$attributes = array(),
		$styles = array(),
		$css_classes = array()
	;
	
	private static 
		$stylesheets = array(),
		$scripts = array(),
		$paths = array(),
		$attachments = array()
	;
	
	private 
		$left_wrap = 'div',
		$right_wrap = 'div',
		$after = false,
		$before = false
	;
	
	private
		$args = array(),
		$location,
		$namespace
	;
	
	private $initialized = false;

	////////////////////////////////////////////////////////
	////////               Public API               ////////
	////////////////////////////////////////////////////////
	
	public function __construct(array $init) {
		$constants = get_defined_constants(true);
		
		foreach ($constants['user'] as $key => $value) {
			if(strstr($key, 'SCARLET_') !== false) {
				$this->path($key, $value);
			}
		}
			
		$this->namespace = $init['namespace'];	
		
		$this->location = loader()->find($this->namespace);
		$this->path('here', dirname($this->location));	
		
		// if(isset($init['args'])) {
		// }
		$this->args($init['args']);
		
		return $this;
	}

	public function stylesheet() {
		$stylesheets = func_get_args();

		if(empty($stylesheets)) {
			return self::$stylesheets;
		}

		foreach ($stylesheets as $sheet) {
			self::$stylesheets[] = $this->_map($sheet);
		}

		return $this;
	}

	public function removeStylesheet() {
		$stylesheets = func_get_args();

		$sheet_arr = self::$stylesheets;
		$sheet_count = count(self::$stylesheets);
		
		foreach ($stylesheets as $sheet) {
			if(is_numeric($sheet)) {
				if($sheet_count > $sheet && $sheet >= 0) {
					for($i = 0; $i < $sheet; $i++) {
						next($sheet_arr);
					}

					unset(self::$stylesheets[key($sheet_arr)]);
				}
			} else {
				$sheet = $this->_map($sheet);
				$index = array_search($sheet, self::$stylesheets);
				if($index !== false) {
					unset(self::$stylesheets[$index]);
				}
			}
		}
		return $this;
	}

	public function script() {
		$scripts = func_get_args();

		if(empty($scripts)) {
			return self::$scripts;
		}

		foreach ($scripts as $script) {
			self::$scripts[] = $this->_map($script);
		}

		return $this;
	}

	public function removeScript() {
		$scripts = func_get_args();

		$sheet_arr = self::$scripts;
		$sheet_count = count(self::$scripts);

		foreach ($scripts as $sheet) {
			if(is_numeric($sheet)) {
				if($sheet_count > $sheet && $sheet >= 0) {
					for($i = 0; $i < $sheet; $i++) {
						next($sheet_arr);
					}

					unset(self::$scripts[key($sheet_arr)]);
				}
			} else {
				$sheet = $this->_map($sheet);
				$index = array_search($sheet, self::$scripts);
				if($index !== false) {
					unset(self::$scripts[$index]);
				}
			}
		}
		return $this;
	}

	public function attach($mixed = null, $value = null, $write = false) {
		if(!isset($mixed)) {
			return self::$attachments;
		} elseif(!isset($value)) {
			if(isset(self::$attachments[$mixed])) {
				return self::$attachments[$mixed];
			} else {
				return '';
			}
		} elseif(defined('SCARLET_ATTACHMENT_DIR')) {
			$path = realpath(SCARLET_ATTACHMENT_DIR);
			if($write) {
				file_put_contents($path.'/'.$mixed, $value);
			} else {
				$value = $this->_map($value);
				copy($value, $path.'/'.$mixed);
			}
			
			// Remove root stuff
			$root = explode('/', $_SERVER['DOCUMENT_ROOT']);
			$path_arr = explode('/', $path);
						
			$path_arr = array_diff($path_arr, $root);
			$path = implode('/', $path_arr);
			$path = trim($path, ' /');
			self::$attachments[$mixed] = '/'.$path.'/'.$mixed;
		}
		
		return $this;
	}

	public function detach() {
		$attachments = func_get_args();
		
		foreach ($attachments as $attachment) {
			if(isset(self::$attachments[$attachment])) {
				unlink($_SERVER['DOCUMENT_ROOT'].'/'.self::$attachments[$attachment]);
				unset(self::$attachments[$attachment]);
			}
		}
		
		return $this;
	}

	public function attr($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return $this->attributes;
		} elseif(is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$this->attributes[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			$this->attributes[$mixed] = $value;
			return $this;
		} elseif(isset($this->attributes[$mixed])) {
			return $this->attributes[$mixed];
		} else {
			return '';
		}
	}
	
	public function removeAttr() {
		$attributes = func_get_args();

		foreach ($attributes as $attribute) {
			if(isset($this->attributes[$attribute])) {
				unset($this->attributes[$attribute]);
			}
		}
		
		return $this;
	}
	
	public function addClass() {
		$classes = func_get_args();
		
		foreach ($classes as $class) {
			$this->css_classes[] = $class;
		}

		$css = implode(' ', $this->css_classes);
		$this->attr('class', $css);

		return $this;	
	}
	
	public function removeClass() {
		$classes = func_get_args();
		
		foreach ($classes as $class) {
			$index = array_search($class, $this->css_classes);
			if($index !== false) {
				unset($this->css_classes[$index]);
			}
		}

		$css = implode(' ', $this->css_classes);
		
		$this->attr('class', $css);

		return $this;
	}
	
	public function style($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return $this->styles;
		} elseif(is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$this->styles[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			$this->styles[$mixed] = $value;
			return $this;
		} elseif(isset($this->styles[$mixed])) {
			return $this->styles[$mixed];
		} else {
			return '';
		}
	}
	
	public function removeStyle() {
		$styles = func_get_args();

		foreach ($styles as $style) {
			if(isset($this->styles[$style])) {
				unset($this->styles[$style]);
			}
		}

		return $this;
	}

	public function wrap($left = 'div', $right = null) {
		// Handles wrap(false);
		// if(!isset($right) && $left === false) {
		// 	$right = false;
		// } else {
		// 	$right = true;
		// }
		
		// Handles wrap('form');
		if(!isset($right)) {
			$right = $left;
		}
		
		if($left === true) {
			$this->left_wrap = 'div';
		} if($right === true) {
			$this->right_wrap = 'div';
		}
		
		if($left === false) {
			$this->left_wrap = false;
		} if($right === false) {
			$this->right_wrap = false;
		}
		
		if(is_string($left)) {
			$this->left_wrap = $left;
		} if(is_string($right)) {
			$this->right_wrap = $right;
		}
		
		return $this;
	}
	
	public function before($before = null) {
		if(isset($before)) {
			$this->before = $before;
		}
		
		return $this;
	}
	
	public function after($after = null) {
		if(isset($after)) {
			$this->after = $after;
		}
		
		return $this;
	}
	
	public function arg($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return $this->args;
		} elseif(is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$this->args[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			$this->args[$mixed] = $value;
			return $this;
		} elseif(isset($this->args[$mixed])) {
			return $this->args[$mixed];
		} else {
			return '';
		}
	}
	
	// Map to arg()
	public function args($mixed = null, $value = null) {
		return $this->arg($mixed, $value);
	}
	
	public function removeArg() {
		$args = func_get_args();

		foreach ($args as $arg) {
			if(isset($this->args[$arg])) {
				unset($this->args[$arg]);
			}
		}

		return $this;
	}
	
	public function defaults() {
		$defaults = func_get_args();
		
		foreach ($defaults as $default) {
			$index = null;
			$i = 0;
			foreach ($this->arg() as $i => $arg) {
				if(is_numeric($i)) {
					$index = $i;
					break;
				}
			}
						
			if(is_array($default)) {
				if(!isset($index)) {
					$this->arg(key($default), current($default));
				} else {
					$this->arg(key($default), $this->arg($index));
					$this->removeArg($index);
				}
			} else {
				if(!isset($index)) {
					if(!isset($arg)) {
						$this->arg($default, '');						
					}
				} else {
					$this->arg($default, $this->arg($index));
					$this->removeArg($index);
				}
			}
			
		}
		
		return $this;
	}
	
	// Deal with later.
	public function useDefaults($namespace = null) {
		if(!isset($namespace)) {
			return $this;
		}
		
		$tag = S($namespace)->args($this->args());
		$tag->init();
		$tag->_initialized(true);
		
		$this->args = $tag->args();
		
		return $this;
	}
	
	// Deal with later.
	public function give() {
		
	}
	
	public function id($id = null) {
		if(isset($id)) {
			$this->attr('id', $id);
			return $this;
		} elseif($this->attr('id')) {
			return $this->attr('id');
		}
				
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$id = '';
		for ($i = 0; $i < 6; $i++) {
	 		$id .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		
		$this->attr('id', $id);
		
		return $id;
	}
	
	public function height($height = null) {
		if(!isset($height)) {
			return $this->style('height');
		}
		
		if(is_numeric($height)) {
			$height .= 'px';
		}
	
		$this->style('height', $height);
		
		return $this;
	}
	
	public function width($width = null) {
		if(!isset($width)) {
			return $this->style('width');
		}
		
		if(is_numeric($width)) {
			$width .= 'px';
		}
		
		$this->style('width', $width);
		
		return $this;
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
			return '';
		}
	}
	
	public function removePath() {
		$paths = func_get_args();

		foreach ($paths as $path) {
			if(isset($this->paths[$path])) {
				unset($this->paths[$path]);
			}
		}

		return $this;
	}

	// public function create($namespace, array $args = array()) {
	// 	if($namespace[0] == '/') {
	// 		$namespace = explode(':', substr($namespace,1));
	// 		$namespace[count($namespace)-1] = 'End'.$namespace[count($namespace)-1];
	// 		$namespace = implode(':',$namespace);
	// 	}
	// 	
	// 	// Load the class
	// 	loader()->add($namespace)->register();
	// 	
	// 	$class = str_replace(':','_',$namespace);
	// 	
	// 	// Params to be sent to Tag
	// 	$tagParams = array();
	// 	$tagParams['location'] = loader()->find($namespace);
	// 	$tagParams['namespace'] = $namespace;
	// 	$tagParams['args'] = $args;
	// 	$tagParams['paths'] = $this->path();
	// 	
	// 	// Creating the tag
	// 	return new $class($tagParams);
	// }
	
	public function location() {
		return dirname($this->location);
	}

	
	public function __tostring() {

		// if($this->_has_runtime_args()) {
		// 	$args = $this->_format_runtime_args($this->args);
		// 	$out = 'new Tag("'.$this->namespace.'", '.$args.')';
		// }
		
		// Since there are no unknowns, we can safely evaluate and
		// return HTML
		// else {		

			// // Initialize the object
			// if(!$this->_initialized()) {
			// 	if(!method_exists($this, 'init')) {
			// 		throw new Exception("init() method required!", 1);
			// 	}
			// 	
			// 	$this->init();
			// 	$this->_initialized(true);
			// }
			
		$out = $this->_render();
			
		// }
		
		return $out;
		
	}
	
	////////////////////////////////////////////////////////
	////////            Internal Functions          ////////
	//////// ONLY USE IF YOU KNOW WHAT YOU'RE DOING ////////
	////////////////////////////////////////////////////////
	
	private function _render() {
		try {  
		
			// If not already initialized.
			if(!$this->_initialized()) {
			    
				if(!method_exists($this, 'init')) {
					throw new Exception("init() method required!", 1);
				}
				
				// Really late initialization, JIT I hope.
				$this->init();
				$this->_initialized(true);
			}
		
			// Get the string representation of the tag
			if(!method_exists($this, 'tostring')) {
				throw new Exception("tostring() method required!", 1);
			}

			$out = $this->tostring();
		

			// Wrap it right up
			$out = $this->_wrapper($out);
		
		} catch(Exception $e) {  
	        trigger_error($e->getMessage(), E_USER_ERROR);  
	        return '';  
	    }
		
		return $out;
	}
	
	public function _initialized($init = null) {
		if(isset($init)) {
			$this->initialized = $init;
			return $this;
		} else {
			return $this->initialized;
		}
	}
	
	// public static function _stylesheets() {
	// 	return self::$stylesheets;
	// }
	// 
	// public static function _scripts() {
	// 	return self::$scripts;
	// }
	
	// public static function _attachments() {
	// 	return self::$attachments;
	// }
	// 
	public static function _clear_stylesheets() {
		self::$stylesheets = array();
	}
	
	public static function _clear_scripts() {
		self::$scripts = array();
	}
	
	public static function _clear_attachments() {
		self::$attachments = array();
	}
	////////////////////////////////////////////////////////
	////////            Private Functions           ////////
	////////////////////////////////////////////////////////
	
	private function _wrapper($out) {
		if($this->left_wrap !== false) {
			// Add Classes
			$b_tag = '<'.$this->left_wrap; 
			
			if(!empty($this->css_classes)) {
				$b_tag .= ' class="'.implode(" ", $this->css_classes).'" ';
			}
			
			// Add styles
			if(!empty($this->styles)) {
				$b_tag .= ' style="';
				foreach ($this->styles as $key=>$value) {
					$b_tag .= $key.':'.$value.';';
				}
				$b_tag .= '" ';
			}
			
			// Generate attributes
			if(!empty($this->attributes)) {
				foreach ($this->attributes as $key => $value) {
					$b_tag .= ' '.$key.' = "'.$value.'" ';
				}	
			}

			// Close tag unless its self enclosing
			if($this->right_wrap != '/') {
				$b_tag .= '>';
			}
			
			// Append to out
			$out = $b_tag.$out;
		}
		
		if($this->right_wrap !== false) {
			// Self-enclosing
			if($this->right_wrap == '/') {
				$e_tag = $this->right_wrap.'>';
			} else {
				$e_tag = '</'.$this->right_wrap.'>';
			}
			$out = $out.$e_tag;
		}
		
		return $out;
	}
	
	public function _map($assert) {
		
		$assert = trim($assert,' /');

		// Allows grabbing asserts from different places in library		
		$path = '';
		if(file_exists(realpath($assert))) {
			$path = realpath($assert);
		}
		elseif(file_exists(realpath('/'.$assert))) {
			$path = realpath('/'.$assert);
		}
		elseif(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$assert)) {
			$path = $_SERVER['DOCUMENT_ROOT'].'/'.$assert;
		}
		elseif (strpos($assert,':') !== false) {

			$namespace = explode(':', $assert);
			$file = array_pop($namespace);
			$file = trim($file, ' /');
			$namespace = implode(':', $namespace);
			$location = dirname(loader()->find($namespace));
			$path = $location.'/'.$file;
		} else {
			$loc = realpath(dirname($this->location));
			$main_dir = $loc;
			$path = $main_dir.'/'.$assert;
		}
		
		if(!file_exists($path)) {
			throw new Exception('Unable to add: '.$path, 1);
		}
		
		return $path;
	}
	
}



?>