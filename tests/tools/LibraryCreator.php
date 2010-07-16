<?php

/** 
* Short Description
*
* Long Description
* @package LibraryCreator
* @author Matt Mueller
*/

function creator($namespace, $library = '/test-library') {
	return new LibraryCreator($namespace, $library);
}

class LibraryCreator 
{
	public 
		$library,
		$namespace,
		$attachments = array(),
		$location
	;
	
	private $case;
	
	function __construct($namespace, $library = 'test-library')
	{
		$this->namespace = $namespace;
		$this->library = $library;
		
		return $this;
	}
	
	public function init() {
		$ns = explode(':',$this->namespace);
		$class = end($ns);
		$nsloc = str_replace(':','/',$this->namespace);

		$file = $this->library.'/'.$nsloc;
		
		$dirs = explode('/', $file);
		array_pop($dirs);

		$found_root = false;
		$path = '';
		foreach ($dirs as $dir) {
			if($dir == '')
				continue;
			elseif(!$found_root) {
				$dir = '/'.$dir;
				$found_root = true;
			}
			
			$path .= $dir;
			if(!is_dir($path)) {
				mkdir($path);
			}
			$path .= '/';
		}
		
		if(!empty($this->attachments)) {
			if(count($ns) == 1)
				$case = 3;
			else
				$case = 2;
		} elseif(count($ns) == 1) {
			$case = rand(1,3);
		} else {
			$case = rand(1,2);
		}
		
		$content = $this->createClass($this->namespace);
		
		switch ($case) {
			case '1':
				// NAMESPACE: slideshow:basic slideshow/basic.php
				file_put_contents($path.'/'.$class.'.php', $content);
				$this->location = $path.'/';
				break;
			case '2':
				// NAMESPACE: slideshow:basic slideshow/basic/basic.php
				if(!is_dir($path.'/'.$class))
					mkdir($path.'/'.$class);
				file_put_contents($path.'/'.$class.'/'.$class.'.php', $content);	
				$this->location = $path.'/'.$class.'/';				
				break;
			case '3':
				// NAMESPACE: slideshow slideshow/slideshow/slideshow.php
				if(!is_dir($path.'/'.$class)) {
					mkdir($path.'/'.$class);	
				}
				
				if(!is_dir($path.'/'.$class.'/'.$class))
					mkdir($path.'/'.$class.'/'.$class);
				
				file_put_contents($path.'/'.$class.'/'.$class.'/'.$class.'.php', $content);
				$this->location = $path.'/'.$class.'/'.$class.'/';
				break;
		}

		foreach ($this->attachments as $attach) {
			$file = $this->location.'/'.$attach;
			
			$dirs = explode('/', $file);

			array_pop($dirs);

			$path = '';
			$found_root = false;
			foreach ($dirs as $dir) {
				if($dir == '')
					continue;
				elseif(!$found_root) {
					$dir = '/'.$dir;
					$found_root = true;
				}
				
				$path .= $dir;
				if(!is_dir($path)) {
					mkdir($path);
				}
				$path .= '/';
			}
			
			file_put_contents($file, '');
		}		
	}
	
	public function attach() {
		$attachments = func_get_args();
		
		foreach ($attachments as $attach) {
			$this->attachments[] = $attach;
		}
	}
	
	public function cleanUp() {
		// exit(0);
		// echo 'rm -rf '.realpath($this->library);echo "<br/>";
		exec('rm -rf '.$this->library);
	}
	
	private function createClass($className) {
	$className = str_replace(":", "_", $className);
	$class = '<?php
		include_once($_SERVER["DOCUMENT_ROOT"]."/Scarlet/Scarlet.php");

		class '.$className.' extends Tag {
			function init() {

			}

			function tostring() {
				return "'.$this->sendToString().'";
			}
		}
	?>';

		return $class;
	}
	
	private function sendToString()
	{
		return '';
	}
}
// 
// $lib = creator("namespace:five");
// $lib->attach("hi.js", "waltz.css", 'louie.png');
// $lib->init();

// $lib->cleanUp();
?>