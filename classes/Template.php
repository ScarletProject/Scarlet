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
		$paths = array()
	;
	
	private 
		$stylesheets = array(),
		$scripts = array(),
		$attachments = array()
	;
	
	private 
		$ignored_content
	;
	
	function __construct($template = 'master.tpl') {
		if(end(explode('.', $template)) != 'tpl') {
			throw new Exception("Not a .tpl file!", 1);
		} elseif(!file_exists($template)) {
			throw new Exception('File doesn\'t exist! '.$template, 1);
		}
		
		$this->template = $template;
		
		define('SCARLET_PROJECT_DIR', dirname(realpath($template)));
		define('SCARLET_ATTACHMENT_DIR', SCARLET_PROJECT_DIR.'/.scarlet');
				// 
				// $this->path('project', dirname(realpath($template)));
				// $this->path('attachment', $this->path('project').'/scarlet');
				// $this->path('scarlet', dirname(realpath('..')));
				// $this->path('default_library', $this->path('scarlet').'/library');

		
		// Load default library
		// loader()->library('default', SCARLET_LIBRARY_DIR);
	}
	
	public function compile() {
		// Recreate attachment directory
		if(!is_dir(SCARLET_ATTACHMENT_DIR)) {
			mkdir(SCARLET_ATTACHMENT_DIR);
		}
		
		$content = $this->parse($this->template);


		// 
		// $attachments = Tag::_attachments();
		// 
		// // Add the attachments to attachment directory
		// foreach ($attachments as $name => $attachment) {
		// 	file_put_contents($attachment['path'], $attachment['content']);
		// }
		
		eval('?>' . $content );
	}
	
	public function parse($template) {
		$content = file_get_contents($template);
		
		$ignore = array(
			"html" => array("<!--", "-->"),
			"php" => array("<?php","?>"), 
			"jeeves" => array("/{","}/"),
			"script" => array("<script","</script>"),
			"css" => array("<style", "</style>"),
		);
		
		$content = $this->hideBlocks($content, $ignore);

		$tags = $this->pull($content);

		for ($i = 0; $i < count($tags); $i++) {
			$tag = $tags[$i];
			$old = '{'.$tag.'}';
			
			$tokens = $this->tokenize($old);

			// Prepare the first function to kick parse_tokens off.
			$function = array_shift($tokens);
			$function = trim(substr($function,1));

			// Post-evaluation tags
			if($function[0] == '&') {
				$new_tag = str_replace('&','',$tags[$i]);	
				$tags[] = $new_tag;
				$content = $this->push($old, '{'.$new_tag.'}', $content);
				continue;
			}

			$Tag = $this->read($tokens, $function);
			
			if(!method_exists($Tag, 'init')) {
				throw new Exception('init method required for: '.$function, 1);
			}
			
			// Initialize it.
			// $Tag->init();
			
			$final = $Tag->__tostring();
	
			/*
			if($Tag->has_runtime_args()) {
				$final = '<?php echo '.$final.'; ?>';
			}
			*/
			
			$content = $this->push($old, $final, $content);
			
		}

		return $content;
	}
	
	private function tokenize($tag) {
		$number = '(?:-?\\b(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][+-]?[0-9]+)?\\b)';
		$oneChar = '([^\0-\x08\x0a-\x1f\\\'"]|\\(?:[\"/\\bfnrt]|u[0-9A-Fa-f]{4})';
		$string = '(?:[\'"]'.$oneChar.'*[\'"])';
		$varName = '\\$(?:'.$oneChar.'[^ \\],}\n\t]*)';
		$func = '(?:{[ \t\n]*'.$oneChar.'[^ \n\t}]*)';
		$assoc = '(?:[\"\']?\w+[\"\']?[ ]*=)';
		
		$scarletToken = '@(?:false|true|null'
		  .'|[\\}\\]\\[]'
		  .'|'.$varName
	      .'|'.$func
		  .'|'.$assoc	    
	      .'|'.$number
	      .'|'.$string
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
					$args[$key] = trim($next_token,'\'"');
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
					
					// if(!method_exists($output, 'init')) {
					// 	throw new Exception('init method required for: '.$function, 1);
					// }
					// $output->init();
					
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
				$args[] = trim($tokens[$i],' "\'');
			}
			else {
				throw new Exception("Cannot parse tokens!", 1);
			}
		}
	}
	
	private function create($namespace, array $args = array()) {
		if($namespace[0] == '/') {
			$namespace = explode(':', substr($namespace,1));
			$namespace[count($namespace)-1] = 'End'.$namespace[count($namespace)-1];
			$namespace = implode(':',$namespace);
		}

		$Tag = S($namespace)->args($args);
				
		$class = str_replace(':','_',$namespace);
		// Allow plugins to be built on the template.
		if(method_exists($this,$method = 'hook_'.$class)) {
			$Tag = $this->$method($Tag);
		}
						
		return $Tag;
	}
	
	private function push($old, $new, $subject) {
		// echo $old;echo " | ";echo htmlspecialchars($new);echo "<br/>";
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
				$content = $this->push($found_tag, '<!--'.$name.'-->', $content);	
				$this->ignored_content[$name][] = $found_tag;
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
	
	// public function path($mixed = null, $value = null) {
	// 	if(!isset($mixed)) {
	// 		return $this->paths;
	// 	} elseif(is_array($mixed)) {
	// 		foreach ($mixed as $key => $value) {
	// 			$this->paths[$key] = $value;
	// 		}
	// 		return $this;
	// 	} elseif(isset($value)) {
	// 		$this->paths[$mixed] = $value;
	// 		return $this;
	// 	} elseif(isset($this->paths[$mixed])) {
	// 		return $this->paths[$mixed];
	// 	} else {
	// 		return '';
	// 	}
	// }
	// 
	// public function removePath() {
	// 	$paths = func_get_args();
	// 
	// 	foreach ($paths as $path) {
	// 		if(isset($this->paths[$path])) {
	// 			unset($this->paths[$path]);
	// 		}
	// 	}
	// 
	// 	return $this;
	// }
	
}


?>