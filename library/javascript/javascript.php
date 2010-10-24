<?php

/**
* Javascript
*/
class Javascript extends Tag {
	private $scripts = array();
	
	public function setup() {
		
		$this->wrap(false);
		
		foreach ($this->arg() as $script) {
			$script = trim($script);
			
			if(is_dir($script)) {
				$tmps = glob(rtrim($script,'/').'/*');
				$scripts = array();
				foreach ($tmps as $tmp) {
					if(stristr($tmp, '.js') !== false) {
						$scripts[] = $tmp;
					}
				}
			} elseif(
				stristr($script, '.js') !== false 
				|| strcmp($script, $this->map($script)) !== 0) {
					$scripts = array($script);
			} else {
				continue;
			}
			
			$this->scripts = array_merge($this->scripts, $scripts);
		}
		
		// print_r($this->scripts);
		
	}

	public function show() {
		$scripts = array();

		$scripts = array_merge($this->script(), $this->scripts);
		$scripts = array_unique($scripts);

		$this->scripts = $scripts;

		if(empty($scripts)) {
			return '';
		}

		foreach($scripts as $i => $script) {
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
			
		}
	
		// Merge all the scripts together
		$scripts = implode("\n\n /* -------------- */ \n\n", $scripts);		
				
		$uid = $this->uid(S()->path('template'));
		$this->attach('scarlet-'.$uid.'.js', $scripts, true);

		$out = '<script src="'.$this->attach('scarlet-'.$uid.'.js').'" type="text/javascript" charset="utf-8"></script>';
		
		return $out;
	}
	
	public function map($script) {
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
			$jquery = $this->location().'/scripts/jquery/jquery.js';
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
			$jquery = $this->location().'/scripts/jquery/jquery-ui.js';
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
			$corners = $this->location().'/scripts/corner.js';
		}
		else {
			// Google location
			$corners = '';
		}
	
		return $corners;
	}
	
	private function json() {
		
		if(true) {
			$json = $this->location().'/scripts/json.js';
		}
		else {
			// Google location
			$json = '';
		}
	
		return $json;
	}
}
?>