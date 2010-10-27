<?php

/** 
* Short Description
*
* Long Description
* @package CSS
* @author Matt Mueller
*/

class CSS extends Tag
{
	private $stylesheets = array();
	private $arg_stylesheets = array();
	private $Tag;
	private $dependency_path;
	
	function setup() {
		$this->wrap(false);		
		
		foreach ($this->arg() as $stylesheet) {
			$stylesheet = trim($stylesheet);
			
			if(is_dir($stylesheet)) {
				$tmps = glob(rtrim($stylesheet,'/').'/*');
				$stylesheets = array();
				foreach ($tmps as $tmp) {
					if(stristr($tmp, '.css') !== false) {
						$stylesheets[] = $tmp;
					}
				}
			} elseif(stristr($stylesheet, '.css') !== false 
				|| strcmp($stylesheet, $this->map($stylesheet)) !== 0) 
			{
				$stylesheets = array($stylesheet);
			} else {
				continue;
			}
			
			$this->stylesheets = array_merge($this->stylesheets, $stylesheets);
		}
		
	}

	public function show() {
		$stylesheets = array_merge($this->stylesheet(), $this->stylesheets);
		$stylesheets = array_unique($stylesheets);
		
		if(empty($stylesheets)) {
			return '';
		}
		
		// Get scripts
		foreach($stylesheets as $i => $stylesheet) {
			$stylesheet = $this->map($stylesheet);
			if(file_exists($stylesheet)) {
				$stylesheets[$i] = file_get_contents($stylesheet);
			} 
			// Definitely redundant - fix later.
			elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet)) {
				
				$stylesheets[$i] = file_get_contents($_SERVER['DOCUMENT_ROOT'].$stylesheet);
			}
			else {
				// $T->error('Unable to retrieve script: '.$stylesheet,__CLASS__,__FUNCTION__,__LINE__);
			}
		}
		
		$stylesheets = implode("\n\n /* -------------- */\n\n", $stylesheets);
		// $scripts = str_replace(array("\n","\t"),"",$scripts);

		$uid = $this->id();
		
		
		// print_r($stylesheets);exit(0);
		// Not perfect, but it will have to do for now.
		// echo "<hr/>";
		// echo "Random Number: ".rand(1,2000);echo "<br/>";
		// echo 'scarlet_'.$uid.'.css';echo "<br/>";
		// echo microtime(true);
		// echo "<hr/>";
		// 
		
		// Create a file based on the template name.
		$template = S()->path('template');
		$uid = $this->uid($template);
		
		$this->createAttachment('scarlet-'.$uid.'.css', $stylesheets);
		// echo $this->attachment('scarlet-'.$uid.'.css');
		// 
		// $from = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
		// $to = $this->attachment('scarlet-'.$uid.'.css');
		// // echo "FROM: $from";echo "<br/>";
		// // echo "TO: $to";echo "<hr/>";
		// $path = Filesystem::absoluteToRelative($from, $to);
		// // echo $path;
		
		$out = '<link rel="stylesheet" href="'.$this->attachment('scarlet-'.$uid.'.css').'" type="text/css" media="screen" title="Scarlet" charset="utf-8" />';
		
		return $out;
	}
	
	public function map($stylesheet) {
		$stylesheet = str_replace('-', '_', $stylesheet);
		if($this->exists($stylesheet)) {
			return $this->$stylesheet();
		} else {
			return $stylesheet;
		}
	}

	private function assert($stylesheet) {
		$this->stylesheets[] = $this->map($stylesheet);
	}

	private function exists($stylesheet = null) {
		if(method_exists($this, $stylesheet))
			return true;
		return false;
	}
	
	private function reset() {
		return $this->location().'/reset.css';
	}
	
	private function rounded() {
		return $this->location().'/rounded/rounded.css';
	}
	
	private function rounded_bottom() {
		return $this->location().'/rounded/rounded-bottom.css';
	}
}




?>