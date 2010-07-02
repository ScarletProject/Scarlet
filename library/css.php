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
	
	function init() {
		$this->wrap(false);
		
		// $this->defaults('stylesheets');
		
		foreach ($this->arg() as $stylesheet) {
			$this->stylesheets[] = $stylesheet;
		}
		
		// 
		// foreach ($this->stylesheet() as $stylesheet) {
		// 	if(is_array($stylesheet)) {
		// 		$stylesheets = array_merge($stylesheets, $stylesheet);
		// 	} else {
		// 		$stylesheets[] = $stylesheet;
		// 	}
		// }
		
	}

	public function tostring() {
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

		$this->attach('scarlet.css', $stylesheets, true);

		$out = '<link rel="stylesheet" href="'.$this->attach('scarlet.css').'" type="text/css" media="screen" title="no title" charset="utf-8" />';
		
		return $out;
	}
	
	private function map($stylesheet) {
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
}




?>