<?php

/** 
* Short Description
*
* Long Description
* @package Tag
* @author Matt Mueller
*/
if(!class_exists('Attribute')) {
	require_once(dirname(__FILE__).'/Attributes.php');
}

class Tag
{
	private
		$attributes = array(),
		$styles = array()
	;
	
	private static 
		$stylesheets = array(),
		$scripts = array(),
		$attachments = array(),
		$given_variables = array(),
		$data = array(),
		$uids = array()
	;
	
	// Assets used for this specific tag
	private
		$used_stylesheets = array(),
		$used_scripts = array()
	;
	
	private 
		$left_wrap = 'div',
		$right_wrap = 'div',
		$wrap_set = false, 
		$after = false,
		$before = false
	;
	
	private
		$args = array(),
		$file,
		$namespace
	;

	private $has_runtime_args;

	private $initialized = false;
	
	
	// To be implemented later! Used to prevent endless recursion
	// private $extends;
	// private static $extend_stack = array();
	// private static $become_stack = array();
	////////////////////////////////////////////////////////
	////////               Public API               ////////
	////////////////////////////////////////////////////////
	
	public function __construct(array $init) {
		$this->namespace = $init['namespace'];	
		$this->file = $this->find($this->namespace);
		
		$this->args($init['args']);
		
		return $this;
	}
	
/*
	public function extend($namespace, $library = null) {
		$this->extends = S($namespace, $library);
	}
*/

	/*
	public function become($namespace, $library = null) {
		$this->becomes = S($namespace, $library);
	}
	*/

	public function stylesheet() {
		$stylesheets = func_get_args();
				
		if(empty($stylesheets)) {
			return self::$stylesheets;
		}


		foreach ($stylesheets as $sheet) {
			if(!isset($sheet) || !$sheet) {
				continue;
			}
			elseif(strstr($sheet, '.') === false) {
				// Defer responsibility to css tag - ie. rounded
				self::$stylesheets[$sheet] = $sheet;
				$this->used_stylesheets[$sheet] = $sheet;
			} elseif(stristr($sheet, '/') !== false) {
				$mapped_sheet = $this->_map($sheet);
				$sheet = basename($sheet);
				self::$stylesheets[$sheet] = $mapped_sheet;
				$this->used_stylesheets[$sheet] = $mapped_sheet;
			} elseif(stristr($sheet, ':') !== false) {
				$mapped_sheet = $this->_map($sheet);				
				self::$stylesheets[$sheet] = $mapped_sheet;
				$this->used_stylesheets[$sheet] = $mapped_sheet;
			} 
			else {
				$mapped_sheet = $this->_map($sheet);
				self::$stylesheets[$this->namespace.':'.$sheet] = $mapped_sheet;
				$this->used_stylesheets[$this->namespace.':'.$sheet] = $mapped_sheet;
			}
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
				// Removes tags like grid
				if(strstr($sheet, '.') !== false) {
					$sheet = $this->_map($sheet);
				}
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
			if(!isset($script) || !$script) {
				continue;
			}
			elseif(strstr($script, '.') === false) {
				// Defer responsibility to javascript tag - ie. jquery
				self::$scripts[$script] = $script;
				$this->used_scripts[$script] = $script;
			} elseif(stristr($script, ':') !== false) {
				$mapped_script = $this->_map($script);				
				self::$scripts[$script] = $mapped_script;
				$this->used_scripts[$script] = $mapped_script;
			}
			elseif(stristr($script, '/')) {
				$mapped_script = $this->_map($script);
				$script = basename($script);
				self::$scripts[$script] = $mapped_script;
				$this->used_scripts[$script] = $mapped_script;
			} else {
				$mapped_script = $this->_map($script);
				self::$scripts[$this->namespace.':'.$script] = $mapped_script;
				$this->used_scripts[$this->namespace.':'.$script] = $mapped_script;
			}
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
				// Removes tags like jquery
				if(strstr($sheet, '.') !== false) {
					$sheet = $this->_map($sheet);
				}
				$index = array_search($sheet, self::$scripts);
				if($index !== false) {
					unset(self::$scripts[$index]);
				}
			}
		}
		return $this;
	}

	// Alias to read only aspect of attach
	public function attachment($mixed = null) {
		return $this->attach($mixed);
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
		} elseif(S()->path('attachments')) {
			$path = S()->path('attachments');
			$file = basename($mixed);
			if($write) {
				file_put_contents($path.'/'.$file, $value);
			} else {
				$value = $this->_map($value);
				copy($value, $path.'/'.$file);
			}
			
			// Remove root stuff
			$root = explode('/', $_SERVER['DOCUMENT_ROOT']);
			$path_arr = explode('/', $path);
						
			$path_arr = array_diff($path_arr, $root);
			$path = implode('/', $path_arr);
			$path = trim($path, ' /');
			self::$attachments[$mixed] = '/'.$path.'/'.$file;
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
	
	public function data($mixed = null, $value = null) {
		if(!isset($mixed)) {
			return $this->data;
		} elseif(is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$this->data[$key] = $value;
			}
			return $this;
		} elseif(isset($value)) {
			$this->data[$mixed] = $value;
			return $this;
		} elseif(isset($this->data[$mixed])) {
			return $this->data[$mixed];
		} else {
			return '';
		}
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
		$classes = implode(' ', $classes);
		$classes = explode(' ', $classes);
		$more = explode(' ', $this->attr('class'));
		$classes = array_merge($more, $classes);
		$classes = array_unique($classes);
		
		$classes = implode(' ', $classes);
		$classes = trim($classes);
		
		$this->attr('class', $classes);

		return $this;	
	}
	
	public function removeClass() {
		$classes = func_get_args();
		$classes_present = explode(' ', $this->attr('class'));
		
		foreach ($classes as $class) {
			$index = array_search($class, $classes_present);
			if($index !== false) {
				unset($classes_present[$index]);
			}
		}

		$css = implode(' ', $classes_present);
		$css = trim($css);
		
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
		$this->wrap_set = true;
		
		// Handles wrap('form');
		if(!isset($right)) {
			$right = $left;
		}
		
		if($left === true) {
			if($this->_leftWrap() == false || $this->_leftWrap() == 'div')
				$this->_leftWrap('div');
		} if($right === true) {
			if($this->_rightWrap() == false || $this->_rightWrap() == 'div')
				$this->_rightWrap('div');
		}
		
		if($left === false) {
			$this->_leftWrap(false);
		} if($right === false) {
			$this->_rightWrap(false);
		}
		
		if(is_string($left)) {
			$this->_leftWrap($left);
		} if(is_string($right)) {
			$this->_rightWrap($right);
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
	
	/*
		TODO Fix issue when key is given, and overwrites defaults
		
		{grid num_cols = '36'}
		
		 -- with this in tag --
		$this->defaults(array('num_cols' => 12))
	*/
	
	public function defaults() {
		$default_arr = func_get_args();
		
		$extras = array();
		$defaults = array();
		foreach ($default_arr as $i => $default) {
			$default = explode('=', $default);
			if(count($default) == 2) {
				$key = rtrim($default[0]);
				$value = ltrim($default[1]);
			} elseif(count($default > 2)) {
				$key = rtrim(array_shift($default));
				$value = ltrim(implode('=', $default));
			} else {
				$key = implode('=',$default);
				$value = false;
			}

			$defaults[$key] = $value;

			if(!$this->arg($key))
				$extras[] = $key;
		}

		$this->arg(array_merge($defaults, $this->arg()));

		for ($i=count($extras)-1; $i >= 0 ; $i--) {
			if($this->arg($i))
				$this->arg($extras[$i], $this->arg($i));
				
			$this->removeArg($i);
		}

		// foreach ($defaults as $default) {
		// 			$index = null;
		// 			$i = 0;
		// 			foreach ($this->arg() as $i => $arg) {
		// 				if(is_numeric($i)) {
		// 					$index = $i;
		// 					break;
		// 				}
		// 			}
		// 						
		// 			if(is_array($default)) {
		// 				if(!isset($index)) {
		// 					$this->arg(key($default), current($default));
		// 				} else {
		// 					$this->arg(key($default), $this->arg($index));
		// 					$this->removeArg($index);
		// 				}
		// 			} else {
		// 				if(!isset($index)) {
		// 					if(!isset($arg)) {
		// 						$this->arg($default, '');						
		// 					}
		// 				} else {
		// 					if(!array_key_exists($default, $this->arg())) {
		// 						$this->arg($default, $this->arg($index));
		// 						$this->removeArg($index);
		// 					}
		// 				}
		// 			}
		// 			
		// 		}
		
		return $this;
	}

	// Deal with later.
	// public function useDefaults($namespace = null) {
	// 	if(!isset($namespace)) {
	// 		return $this;
	// 	}
	// 	
	// 	$tag = S($namespace)->args($this->args());
	// 	$tag->init();
	// 	$tag->_initialized(true);
	// 	
	// 	$this->args = $tag->args();
	// 	
	// 	return $this;
	// }
	// 
	
	public function give($sheet = null, $mixed = null, $value = null) {
		if(!isset($sheet) || !isset($mixed)) {
			return $this;
		}
		
		$sheet_parts = explode('.', $sheet);
		$suffix = end($sheet_parts);
		
		if(!($suffix == 'css' || $suffix == 'js')) {
			throw new Exception("Give only works with CSS or JS files right now!", 1);
		}
		
		$file = $this->_map($sheet);

		$content = file_get_contents($file);
		
		if(!is_array($mixed)) {
			$mixed = array($mixed => $value);
		}
		
		// // Add id and class to the mix if added to script or css file
		// if(!isset($mixed['id'])) {
		// 	$mixed['id'] = '#'.trim(ltrim($this->id(), '#'));
		// } elseif(!isset($mixed['class'])) {
		// 	$mixed['class'] = str_replace(' ', '.', trim($this->attr('class')));
		// }
		// 
		
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $content, $matches);
		
		$vars = array();
		foreach ($matches[0] as $match) {
			$vars[$match] = '';
		}
		
		$vars = array_keys($vars);
		$has_at_element = false;
		foreach($vars as $variable) {			
			// Replace variables	
			$var = str_replace('$', '', $variable);
			if(isset($mixed[$var])) {
				$value = $mixed[$var];
			} elseif($var == 'element') {
				// Replace me later when I know a little more
				$has_at_element = true;
				continue;
			} else {
				// All variables in asset should be accounted for..
				// Tags should be middlemen and handle any undefined give variables
				throw new Exception("Variable: $variable is not present in given variables", 1);
				
			}
							
			if($suffix == 'js') {
				$replace = $this->_php_to_javascript_var($value);
			} else {
				$replace = $value;
			}

			$content = str_replace($variable, $replace, $content);			
		}

		$sheet = explode('.', $sheet);
		// Suffix already used - use $end instead.
		$end = array_pop($sheet);
		
		$message = array();
		foreach($mixed as $key => $value) {
			$message[] = $key.':'.$value;
		}
		$message = implode(';',$message);
		$message = $file.'|'.$message;
		
		$suid = 's'.$this->uid($message, 4);
		
		
		if($has_at_element) {
			$content = str_ireplace('$element', $suid, $content);
			$this->addClass($suid);
		}

		
		$sheet = implode('.', $sheet);
		$sheet = $sheet.'_'.$suid.'.'.$end;
		
		$this->attach($sheet, $content, true);
		
		if($suffix == 'js') {
			$this->script($this->attach($sheet));
		} else {
			$sheet = $this->attach($sheet);
			$this->stylesheet($sheet);
		}
		
		
		return $this;
	}
	
	// Used to figure out if our attach needs to be unique
	// Used helper because variable names were getting really messy.
	private function givenVariablesExist($file, $variables) {
		// If given variables unique, attach a unique sheet
		if(isset(self::$given_variables[$file])) {
			foreach(self::$given_variables[$file] as $present_id => $present_vars) {
				$unique = false;
				foreach($variables as $key => $value) {
					if(!isset($present_vars[$key])) {
						throw new Exception("This shouldn't happen", 1);
					} elseif($value !== $present_vars[$key]) {
						$unique = true;
					}
				}
				
				if(!$unique) {
					return $present_id;
				}
			}
			
			return false;
		} else {
			self::$given_variables[$file] = array();
			return false;
		}
	}
	
	public function id($id = null) {
		if(isset($id)) {
			$this->attr('id', $id);
			return $this;
		} elseif($this->attr('id')) {
			return $this->attr('id');
		}
		
		$args = $this->arg();
		$message = $this->namespace.' '.implode(',',$args);
		$id = $this->uid($message);

		// Perhaps useful at some point...
		// $count = 0;
		// while(isset(self::$uids[$id])) {
		// 	$md5 = md5($message.$count);
		// 	$id = substr($md5, 0, $strlen);
		// 	$count++;
		// }
		// 
		// self::$uids[$id] = true;
		
		$this->attr('id', $id);
		
		return $id;
	}
	
	private function uid($message, $strlen = 6) {
				
		$md5 = md5($message);
		$id = substr($md5, 0, $strlen);
		

		
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
	
	// public function show() {
	// 	echo 'ookokz';
	// 	return $this->_render();
	// }
	
	public function __tostring() {
		$out = $this->_render();
		return $out;
	}
	
	////////////////////////////////////////////////////////
	////////            Internal Functions          ////////
	//////// ONLY USE IF YOU KNOW WHAT YOU'RE DOING ////////
	////////          ARE SUBJECT TO CHANGE!        ////////
	////////////////////////////////////////////////////////
	
	public function _used_scripts() {
		return $this->used_scripts;
	}
	
	public function _used_stylesheets() {
		return $this->used_stylesheets;
	}
	
	public function _leftWrap($wrap = null) {
		if(!isset($wrap)) {
			return $this->left_wrap;
		} else {
			$this->left_wrap = $wrap;
			return $this;
		}
	}
	
	public function _rightWrap($wrap = null) {
		if (!isset($wrap)) {
			return $this->right_wrap;
		} else {
			$this->right_wrap = $wrap;
			return $this;
		}
	}
	
	public function location($namespace = null) {
		if(isset($namespace)) {
			return S()->location($namespace);
		} else {
			return dirname($this->file);
		}
	}
	
	public function find($namespace = null) {
		if(isset($namespace)) {
			return S()->find($namespace);
		} else {
			return $this->file;
		}
	}
	
	public function library() {
		$libraries = func_get_args();
		S()->library($libraries);
		// throw new Exception("Unsafe Operation: Use S()->library(".implode(', ', $libraries).") instead", 1);
		return $this;
	}
	
	public function removeLibrary() {
		$libraries = func_get_args();
		S()->removeLibrary($libraries);
		// throw new Exception("Unsafe Operation: Use S()->removeLibrary(".implode(', ', $libraries).") instead", 1);
		return $this;
	}
	
	public function _initialized($init = null) {
		if(isset($init)) {
			$this->initialized = $init;
			return $this;
		} else {
			return $this->initialized;
		}
	}
	
	public function _has_runtime_args() {
		if(isset($this->has_runtime_args)) {
			return $this->has_runtime_args;
		}
		
		foreach ($this->arg() as $i => $arg) {
			
			if(is_array($arg)) {
				
				$verdict = $this->_has_runtime_args_helper($arg);
				if($verdict) {
					$this->has_runtime_args = true;
					return true;
				}
			}
			elseif($arg instanceof Tag) {
				$verdict = $this->_has_runtime_args_helper($arg->args());
				if($verdict) {
					$this->has_runtime_args = true;
					return true;
				}
			}
			elseif(is_string($arg)) {
				if(isset($arg[0]) && $arg[0] == '$') {
					$this->has_runtime_args = true;
					return true;
				} elseif(strpos($arg, 'S(') !== false) {
					$this->has_runtime_args = true;
					return true;
				}
			}
		}
		
		$this->has_runtime_args = false;
		return false;
	}
	
	private function _has_runtime_args_helper(array $args = array()) {
		// Now check args
		
		foreach ($args as $i => $arg) {
			
			if(is_array($arg)) {
				$verdict = $this->has_runtime_args_helper($arg);
				if($verdict) {
					return true;
				}
			}
			elseif($arg instanceof Tag) {
				$verdict = $this->has_runtime_args_helper($arg->args());
				if($verdict) {
					return true;
				}
			}
			elseif(is_string($arg)) {
				if($arg[0] == '$') {
					return true;
				} elseif(strpos($arg, 'S(') !== false) {
					return true;
				}
			}
		}

		return false;
	}

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
	private function _render() {
		
		try {
			// If not already initialized.
			if(!$this->_initialized()) {
			    

				
				// Add all the normal attributes
				foreach ($this->arg() as $key => $value) {
					if(is_numeric($key)) continue;

					// Make it easy for people to add classes (getting around PHP's use of class)
					if($key == 'class') {
						$key = 'attr_'.$key;
					}

					// Map to Attribute class if method exists
					if(method_exists('Attribute', $key)) {
						call_user_func(array('Attribute', $key), $value, $this);				
					}
				}
				
				// Add scarlet class
				$class = str_replace(':', '-', $this->namespace);
				$class = strtolower($class);
				$this->addClass('scarlet-'.$class);
					
				// Really late initialization, JIT - I hope!
				if(!method_exists($this, 'setup')) {
					throw new Exception("setup() method required!", 1);
				}
				$this->setup();
				$this->_initialized(true);
		
				/*
				if(isset($this->becomes)) {				
					$this->_becomer();
					return $this->becomes->_render();
				}*/

			}
			
			if(isset($this->extends)) {
				$this->_extender();
			}
			
		// 	S($this->namespace);
		// exit(0);
			// Get the string representation of the tag
			
			if(method_exists($this, 'show')) {
				$out = $this->show();
			} else {
				$out = '';
			}
			// exit(0);

			if($this->_has_runtime_args()) {
				// Re-map out to be called at runtime
				$out = 'S("'.$this->namespace.'", '.$this->_format_runtime_args($this->arg()).')';
			} else {
				// Wrap it right up
				$out = $this->_wrapper($out);
			}
		
			// Remove themed (temporary) library
			if($this->arg('theme')) {
				S()->removeLibrary(S()->path('themes').'/'.$this->arg('theme'));
			}
		
		} catch(Exception $e) {  
	        trigger_error($e->getMessage(), E_USER_ERROR);  
	        return ''; 
	    }
		
		return $out;
	}
	
	private function _wrapper($out) {
		if($this->left_wrap !== false) {
			// Add Classes
			$b_tag = '<'.$this->left_wrap; 

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
		
		if($this->before !== false) {
			$out = $this->before.$out;
		}
		
		if($this->after !== false) {
			$out = $out.$this->after;
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
			$path = S()->findAsset($assert);
		} else {
			$assert = $this->namespace.':'.$assert;
			$path = S()->findAsset($assert);
		}
		
		if(!file_exists($path)) {
			echo $path;echo "<br/>";
			throw new Exception('Unable to map: '.$path.' to right location. ('.$assert.')', 1);
		}
		
		return $path;
	}
	
	private function _extender() {
		$e = $this->extends;
		if($e->_initialized()) {
			return $this;
		}

		// For now just take out all the defaults
		// $args = array();
		// foreach ($this->arg() as $key => $value) {
		// 	if(!is_numeric($key)) {
		// 		$args[$key] = $value;
		// 	}
		// }

		$e->arg($this->args());
		$e->_render();
		$e->_initialized(true);

		// Will all be overwritten as needed
		$this->addClass($e->attr('class'));
		$this->style($e->style());
		$this->attr($e->attr());
		
		// If you didn't set wrap, let extended decide how to wrap it.
		if(!$this->wrap_set) {
			$this->wrap($e->_leftWrap(), $e->_rightWrap());
		}
		
		return $this;
	}
/*

	private function _becomer() {
		$b = $this->becomes;
		$b->args($this->args());
	}

*/
	private function _php_to_javascript_var($val) {
		$out = '';
		if(is_string($val)) {
			$out .= $val;
		}
		// Array args going to need to be recursive to be fully functional..
		elseif(is_array($val)) {
			foreach ($val as $i => $v) {
				$val[$i] = addslashes($v);
			}
			$val = implode('","', $val);
			$val = '["'.$val.'"]';
			$out .= $val;
		}
		elseif(is_numeric($val) || is_bool($val))
			$out .= $val;
		else
			$this->error("Unable to convert PHP args to JS",__CLASS__,__FUNCTION__,__LINE__);

		return $out;
	}
	
	private function _format_runtime_args($args) {
		$str_arr = array();
		foreach ($args as $i => $arg) {
			// If key is associative, add quotes
			if(is_string($i)) {
				$i = '"'.$i.'"';
			}

			if(is_array($arg)) {
				$str_arr[] = $i.' => '.$this->format_runtime_args($arg);
			}
			elseif($arg instanceof Tag) {
				$str_arr[] = $i.' => S("'.$arg->namespace.'",'.$arg->format_runtime_args($arg->args()).')';
			}
			elseif($arg[0] == '$') {
				$str_arr[] = $i.' => '.$arg;
			}
			elseif(is_string($arg)) {
				$pos = strpos($arg, '(');
				$func = substr($arg,0,$pos);

				if(is_callable($func)) {
					$str_arr[] = $i.' => '.$arg;
				}
				else {
					$str_arr[] = $i.' => '.'"'.$arg.'"';
				}
			}
			elseif(is_numeric($arg)) {
				$str_arr[] = $i.' => '.$arg;
			}
			elseif(is_bool($arg)) {
				$str_arr[] = $i.' => '.$arg;
			}

		}

		$str_args = implode(', ', $str_arr);
		$str_args = 'array( '.$str_args.' )';
		return $str_args;
	}

}



?>