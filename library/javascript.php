<?php

/**
* Javascript
*/
class Javascript extends Tag {
	private $scripts = array();
	
	public function init() {
		
		$this->wrap(false);
		
		$scripts = array();
		
		foreach ($this->args() as $script) {
			if(is_array($script)) {
				$this->scripts = array_merge($this->scripts, $script);
			} else {
				$this->scripts[] = $script;
			}
		}
	}

	public function tostring() {
		$scripts = array();

		$scripts = array_merge($this->script(), $this->scripts);
		$scripts = array_unique($scripts);

		$this->scripts = $scripts;

		if(empty($scripts)) {
			return '';
		}

		
		// $scriptVars = Tag::_script_vars();
		foreach($scripts as $i => $script) {
			$vars = array();
			// Gets the path of scripts like 'jquery'
			if(isset($scriptVars[$script])) {
				$vars = $scriptVars[$script];
			}
			
			$script = $this->map($script);

			if(file_exists($script)) {				
				$scripts[$i] = file_get_contents($script);
			} 
			elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$script)) {
				$scripts[$i] = file_get_contents($_SERVER['DOCUMENT_ROOT'].$script);
			}
			else {
				throw new Exception('Unable to retrieve script: '.$script, 1);
			}
			
			// Add the closure
			if(!empty($vars)) {

				$keys = array_keys($vars);
				foreach ($keys as $j => $key) {
					if(is_numeric($key)) {
						$keys[$j] = 'arg'.$key;
					}
				}
				$keys = implode(', ', $keys);
				$vars = implode(', ', $vars);
				
				$scripts[$i] = '(function('.$keys.'){'.$scripts[$i].'})('.$vars.');';
			}
			
		}
	
		// Merge all the scripts together
		$scripts = implode("\n\n /* -------------- */ \n\n", $scripts);		
		
		// Add to .Scarlet directory
		$this->attach('scarlet.js', $scripts, true);

		$out = '<script src="'.$this->attach('scarlet.js').'" type="text/javascript" charset="utf-8"></script>';
		
		return $out;
	}
	
	private function map($script) {
		if($this->exists($script)) {
			return $this->$script();
		} else {
			return $script;
		}
	}
	
	private function assert($script) {
		$this->scripts[] = $this->map($script);
	}

	private function exists($script = null) {
		if(method_exists($this, $script))
			return true;
		return false;
	}

	private function jquery() {
		if(true) {
			$jquery = '/Javascript/jQuery/jquery.js';
		}
		else {
			// Google location
			$jquery = '';
		}
	
		return $jquery;
	}

	private function jqueryui() {
		$this->assert('jquery');
		
		if(true) {
			$jquery = '/Javascript/jQuery/jquery-ui.js';
		}
		else {
			// Google location
			$jquery = '';
		}
	
		return $jquery;
	}

	private function corners() {
		$this->assert('jquery');
		
		if(true) {
			$corners = '/Javascript/corner.js';
		}
		else {
			// Google location
			$corners = '';
		}
	
		return $corners;
	}
	
	private function json() {
		
		if(true) {
			$json = '/Javascript/json.js';
		}
		else {
			// Google location
			$json = '';
		}
	
		return $json;
	}
	
	private function cycle() {
		$this->assert('jquery');

		if(true) {
			$json = '/Javascript/cycle.js';
		}
		else {
			// Google location
			$json = '';
		}
	
		return $json;
	}
}
?>