<?php

/** 
* Short Description
*
* Long Description
* @package Template
* @author Matt Mueller
*/

require_once(dirname(__FILE__).'/../Scarlet.php');

class Template 
{
	private $template;

	private 
		$stylesheets = array(),
		$scripts = array(),
		$attachments = array()
	;
	
	private static
		$variables = array()
	;
	
	private 
		$ignored_content,
		$include_scarlet = false
	;
	
	function __construct($template = null) {
		// if(end(explode('.', $template)) != 'tpl') {
		// 	throw new Exception("Not a .tpl file!", 1);
		// }
		if(!isset($template)) {
			return $this;
		}
		
		if(!file_exists($template)) {
			throw new Exception('File doesn\'t exist! '.$template, 1);
		}
		
		$this->template = realpath($template);
		
		S()->path('template', dirname($this->template));
	}
	
	public function show() {
		// If live, take the compiled version, otherwise compile, then eval()
		
		if(!isset($this->template)) {
			throw new Exception(
				"Unable to show: No template defined (example usage: S('main.tpl')->show(); )", 1);
		}
		
		// Default to development..
		if(!S()->stage()) {
			S()->stage('development');
		}
		
		$file = $this->findCompiledFile();
		
		// Turn the assigned variables into real variables - but obscure current scope variables first.
		$scarlet_file = $file;
		
		// Get all assigned variables.
		$assignments = S()->assign();
		if(!empty($assignments)) {
			extract($assignments, EXTR_OVERWRITE);
		}
		
		if(S()->stage('live')) {
			
			// Compiling is expensive - do it once, when you switch over from development to live
			if(!file_exists($scarlet_file)) {
				$this->compile();
			}
			
			if(!file_exists($scarlet_file)) {
				throw new Exception("Not sure what happened: Unable to find compiled file at $scarlet_file", 1);
			}
			
			// Include the file
			include_once($scarlet_file);
		} else {
			// Remove the compiled file if it exists
			if(file_exists($file)) {
				unlink($file);
			}
			
			$scarlet_content = $this->fetch();
			eval('?>'.$scarlet_content);
		}
	}
	
	public function fetch($content = null) {
		if(isset($content)) {
			$out = $this->parse($content, true);
		} elseif(isset($this->template)) {
			$out = $this->parse($this->template);
		} else {
			throw new Exception("Unable to fetch: No template or content defined", 1);
		}

		return $out;
	}
	
	public function __tostring() {
		return $this->fetch();
	}
	
	// Alias to Scarlet class made.
	public function assign($name = null, $value = null) {
		if(!isset($name) || !isset($value))
			return $this;
		else {
			S()->assign($name, $value);
		}
		
		return $this;
	}
	
	private function _clearCompiled() {
		if(file_exists())
		if(is_dir(S()->path('compiled')) && S()->path('compiled')) {
			exec('rm -r '.S()->path('compiled'));
		}
	}
	
	// Should place finished product in compiled folder
	public function compile($content = null) {
		
		if(!S()->path('compiled')) {
			throw new Exception("Cannot compile: Scarlet needs to be initialized first - to initialize run S()->init(...path-to-projects-scarlet-dir...)", 1);
		}
		
		// Create the compiled directory
		if(!is_dir(S()->path('compiled')))
			mkdir(S()->path('compiled'));

		if(isset($content)) {
			$content = $this->fetch($content);
		}
		else {
			$content = $this->fetch();
		}
		
		// $header = '<!-- Compiled by Scarlet at '.date("g:ia").' on '.date('M j Y').' -->'."\n\n";
		// $content = $header.$content;

		$filepath = $this->findCompiledFile();

		S()->mkdir(dirname($filepath));
		
		// echo S()->path('compiled').'/'.$file;exit(0);
		file_put_contents($filepath, $content);
	}
	
	public function isCompiled() {
		if(!is_dir(S()->path('compiled'))) {
			return false;
		}
		
		if(file_exists($this->findCompiledFile())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function findCompiledFile() {
		$file = explode('.', basename($this->template));
		$suffix = array_pop($file);
		
		// Append $file.scarlet.php
		array_push($file, 'scarlet', 'php');
		$file = implode('.', $file);

		// Find folder difference
		$template = explode('/',dirname($this->template));
		$project = explode('/', S()->path('project'));
		// Pop off Scarlet directory
		array_pop($project);
		
		// Resolve differences
		$diff = array_diff($template, $project);
		$diff = implode('/', $diff);
		
		return S()->path('compiled').'/'.$diff.'/'.$file;
	}
	
	private function parse($template = null, $is_content = false) {
		if(!isset($template)) {
			$template = $this->template;
		}
		if(!$is_content) {
			$content = file_get_contents($template);
		} else {
			$content = $template;
		}
		
		
		$ignore = array(
			"html" => array("<!--", "-->"),
			"php" => array("<?php","?>"), 
			"jeeves" => array("/{","}/"),
			"script" => array("<script","</script>"),
			"css" => array("<style", "</style>"),
		);
		
		$content = $this->hideBlocks($content, $ignore);

		// Pull the tags out of the document
		$tags = $this->pull($content);

		// Allows for JS, css shortcut to be made
		$found_css = false;
		$found_js = false;

		for ($i = 0; $i < count($tags); $i++) {
			$tag = $tags[$i];
			$old = '{'.$tag.'}';
			
			$tokens = $this->tokenize($old);


			// Prepare the first function to kick parse_tokens off.
			$function = array_shift($tokens);
			$function = trim(substr($function,1));

			// Helper functions
			if(strcasecmp($function, 'CSS') == 0 && !$found_css) {
				$function = '&'.$function;
				$found_css = true;
			} elseif(strcasecmp($function, 'javascript') == 0 && !$found_js) {
				$function = '&'.$function;
				$found_js = true;
			}

			// Post-evaluation tags
			if($function[0] == '&') {
				$new_tag = str_replace('&','',$tags[$i]);
				$tags[] = $new_tag;
				$content = $this->push($old, '{'.$new_tag.'}', $content);
				continue;
			}
			
			if($function[0] == '$') {
				$final = '<?php echo '.$function.'; ?>';
				$content = $this->push($old, $final, $content);
				continue;
			}
			
			$Tag = $this->read($tokens, $function);


			$final = $Tag->__tostring();
	
			
			if($Tag->_has_runtime_args()) {
				$this->include_scarlet = true;
				$final = '<?php echo '.$final.'; ?>';
			}
			
			
			$content = $this->push($old, $final, $content);
			
		}
		
		$content = $this->showBlocks($content);

		// Include Scarlet if there are runtime args
		if($this->include_scarlet) {
			$included = get_included_files();
			$scarlet = array_filter($included, array($this, '_findScarlet'));
			$scarlet = implode('', $scarlet);
			
			$php = '<?php require_once("'.$scarlet.'");';
			$php .= 'S()->init("'.S()->path('project').'"); ?>';
			$content = $php.$content;
		}
		
		return $content;
	}
	
	private function _findScarlet($file) {
		if(strstr($file, 'Scarlet.php') !== false) {
			return true;
		}
		else
			return false;
	}
	
	private function tokenize($tag) {
		// Modified version of JSON's parser
		$number = '(?:-?\\b(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][+-]?[0-9]+)?\\b)';
		$oneChar = '([^\0-\x08\x0a-\x1f\\\'"]|\\(?:[\"/\\bfnrt]|u[0-9A-Fa-f]{4})';
		$string = '(?:[\'"]'.$oneChar.'*[\'"])';
		$varName = '\\$(?:'.$oneChar.'[^ \\],}\n\t]*)';
		$func = '(?:{[ \t\n]*'.$oneChar.'[^ \n\t}]*)';
		$assoc = '(?:[\"\']?[\w-.:]+[\"\']?[ ]*=)';
		$attr = '(?:[\w\-.:]+)';
		
		$scarletToken = '@(?:false|true|null'
		  .'|[\\}\\]\\[]'
		  .'|'.$varName
	      .'|'.$func
		  .'|'.$assoc	    
	      .'|'.$number
	      .'|'.$string
		  .'|'.$attr
	      .')@';		
		
		preg_match_all($scarletToken, $tag, $out);
		
		return $out[0];
	}
	
	private function read($tokens, $function, $offset = 0, $depth = 0) {
		$args = array();

		for ($i = $offset; $i < count($tokens); $i++) { 
			$tokens[$i] = trim($tokens[$i]);
			
			// Last character is =, then associative
			if($tokens[$i][strlen($tokens[$i])-1] == '=') {

				$key = substr($tokens[$i], 0, strlen($tokens[$i])-1);
				$key = trim($key,'\'" ');

				// Look ahead to the next token
				$next_token = $tokens[$i+1];
				// Next token will always be second half of associate array
				$i++;
				if($next_token[0] == '$') {
					$args[$key] = $next_token;
				}
				elseif($next_token[0] == '{') {
					$func = substr($tokens[$i], 1);
					$result = $this->read($tokens, $func, $i+1, $depth+1);
					$args[$key] = $result['out'];
					$i = $result['offset'];
				}
				elseif($next_token == '[') {
					$result = $this->read($tokens, $function, $i+1, $depth+1);
					$args[$key] = $result['out'];
					$i = $result['offset'];
				}
				elseif(is_numeric($next_token)) {
					$args[$key] = $next_token;
				}
				elseif(is_string($next_token)) {
					// Boolean tests
					if($tokens[$i] == 'true') {
						$args[$key] = true;
					} elseif($tokens[$i] == 'false') {
						$args[$key] = false;
					} else {
						// Otherwise its a string
						$args[$key] = trim($next_token,'\'"');
					}
				}
				else {
					throw new Exception("Cannot parse tokens!", 1);
				}
				continue;
			}
			elseif($tokens[$i][0] == '{') {
				$func = substr($tokens[$i], 1);
				$result = $this->read($tokens, $func, $i+1, $depth+1);
				$args[] = $result['out'];
				$i = $result['offset'];
				continue;
			}
			elseif($tokens[$i] == '}') {
				if($depth == 0) {
					return $this->create($function, $args);
				} else {
					$output = $this->create($function, $args);
					
					// Initialize the object
					if(!$output->_initialized()) {
						if(!method_exists($output, 'init')) {
							throw new Exception("init() method required!", 1);
						}

						$output->init();
						$output->_initialized(true);
					}
					
					return array('out'=>$output,'offset'=>$i);
				}
			}
			elseif($tokens[$i] == '[') {
				$result = $this->read($tokens, $function, $i+1, $depth+1);
				$args[] = $result['out'];
				$i = $result['offset'];
				continue;
			}
			elseif($tokens[$i] == ']') {				
				return array('out'=>$args, 'offset'=>$i);
			}
			elseif($tokens[$i][0] == '$') {
				$args[] = $tokens[$i];
			}
			elseif(is_numeric($tokens[$i])) {
				$args[] = $tokens[$i];
			}
			elseif(is_string($tokens[$i])) {
				if(preg_match('/[\'"]/', $tokens[$i])) {
					// If its a string
					$args[] = trim($tokens[$i], ' "\'');
				} elseif($tokens[$i] == 'true') {
					// Boolean tests
					$args[] = true;
				} elseif($tokens[$i] == 'false') {
					$args[] = false;
				} else {
					// Otherwise its an attribute
					$key = $tokens[$i];	
					$args[$key] = true;
				}
			}
			else {
				throw new Exception("Cannot parse tokens!", 1);
			}
		}
	}
	
	private function create($namespace, array $args = array()) {

		$Tag = S($namespace)->args($args);		

		$class = str_replace(':','_',$namespace);
		// Allow plugins to be built on the template.
		if(method_exists($this,$method = 'hook_'.$class)) {
			$Tag = $this->$method($Tag);
		}
						
		return $Tag;
	}
	
	private function push($old, $new, $subject) {
		$pos = strpos($subject,$old);
		
		if ($pos !== false)
		    return substr_replace($subject,$new,$pos,strlen($old));
		else
			throw new Exception("Cannot find tag to replace (old: $old, new: ".htmlspecialchars($new).")", 1);
	}
	
	private function pull($str, $start = '{', $end = '}') {
		$tags = array();
		$nest = -1;
		$start_mark = 0;
		
		for ($i=0; $i < strlen($str); $i++) { 
			$start_substr = substr($str, $i, strlen($start));
			$end_substr = substr($str, $i, strlen($end));
			
			if($start_substr == $start) {
				$nest++;
				if($nest == 0) {
					$start_mark = $i;
				}
			}
			elseif($end_substr == $end) {
				if($nest == 0) {
					// $tags[] = substr($str, $start_mark, $i+strlen($end));
					$tags[] = substr($str, $start_mark + strlen($start), $i - $start_mark - strlen($start));
					$start_mark = $i;
				}
				$nest--;
			}
		}
		
		if($nest != -1) {
			throw new Exception("Unable to parse - probably forgot a curly!", 1);
		}
		
		return $tags;
	}
	
	private function hideBlocks($content, $ignore) {
		foreach ($ignore as $name => $tags) {
			$this->ignored_content[$name] = array();
			$start = $tags[0];
			$end = $tags[1];

			$found_tags = $this->pull($content, $start, $end);

			foreach ($found_tags as $found_tag) {
				$content = $this->push($start.$found_tag.$end, '<!--'.$name.'-->', $content);	
				$this->ignored_content[$name][] = $start.$found_tag.$end;
			}
		}

		return $content;
	}
	
	private function showBlocks($content) {
		foreach ($this->ignored_content as $language => $group) {
			foreach ($group as $replace) {
				$content = $this->push('<!--'.$language.'-->',$replace, $content);
			}
		}
			
		$this->ignored_content = array();
		
		return $content;
	}

}


?>