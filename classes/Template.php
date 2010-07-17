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
	
	private 
		$ignored_content
	;
	
	function __construct($template) {
		// if(end(explode('.', $template)) != 'tpl') {
		// 	throw new Exception("Not a .tpl file!", 1);
		// }
		if(!isset($template)) {
			throw new Exception('Need to include a template');
		}
		
		if(!file_exists($template)) {
			throw new Exception('File doesn\'t exist! '.$template, 1);
		}
		
		$this->template = realpath($template);
		
		S()->path('template', dirname($this->template));
	}
	
	public function compile() {

		$content = $this->parse($this->template);

		eval('?>' . $content );
	}
	
	public function __tostring() {
		$this->compile();
		
		return '';
	}
	
	public function parse($template, $is_content = false) {
		if($is_content) {
			$content = $template;
		} else {
			$content = file_get_contents($template);
		}
		
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

			$final = $Tag->__tostring();
	
			/*
			if($Tag->has_runtime_args()) {
				$final = '<?php echo '.$final.'; ?>';
			}
			*/
			
			$content = $this->push($old, $final, $content);
			
		}
		
		$content = $this->showBlocks($content);

		// echo htmlspecialchars($content);
		// exit(0);
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
	
	public function projectPath($path) {
		S()->projectPath($path);
		
		return $this;
	}
}


?>