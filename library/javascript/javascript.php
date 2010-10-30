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
		
		foreach ($this->attach() as $name => $attachment) {
			$file = basename($attachment);
			$extension = end(explode('.', $attachment));
			if($extension == 'js') {
				$scripts[$name] = $attachment;
			}
		}

		$scripts = array_merge($scripts, $this->scripts);
		$scripts = array_unique($scripts);

		if(empty($scripts)) {
			return '';
		}

		// Get scripts
		foreach($scripts as $i => $script) {
			$script = $this->map($script);
			if(file_exists($script)) {
				$scripts[$i] = file_get_contents($script);
			} 
		}
		// print_r($scripts);
		$combined = implode("\n\n /* -------------- */\n\n", $scripts);
		// $scripts = str_replace(array("\n","\t"),"",$scripts);


		// print_r($scripts);exit(0);
		// Not perfect, but it will have to do for now.
		// echo "<hr/>";
		// echo "Random Number: ".rand(1,2000);echo "<br/>";
		// echo 'scarlet_'.$uid.'.js';echo "<br/>";
		// echo microtime(true);
		// echo "<hr/>";
		// 

		// Create a file based on the template name.
		$template = S()->path('template');
		$uid = $this->uid($template);

		$this->attach('scarlet-'.$uid.'.js', $combined, true);
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