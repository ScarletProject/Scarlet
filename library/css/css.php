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
		$stylesheets = array();
		foreach ($this->attach() as $name => $attachment) {
			$file = basename($attachment);
			$extension = end(explode('.', $attachment));
			if($extension == 'css') {
				$stylesheets[$name] = $attachment;
			}
		}
		
		$stylesheets = array_merge($stylesheets, $this->stylesheets);
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
		}
		// print_r($stylesheets);
		$combined = implode("\n\n /* -------------- */\n\n", $stylesheets);
		// $scripts = str_replace(array("\n","\t"),"",$scripts);
		
		
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
		
		// Rework all the paths in CSS
		$urls = $this->_get_urls($combined);
			

		
		// Create a blank file and get a link to it.
		$this->attach('scarlet-'.$uid.'.css', '', true);
		
		
		// Implement just a little bit of magic here:
			// Basically allow CSS to also use urls relative to template -
			// like every other implementation......
		$template_path = dirname(S()->path('template'));
		
		$paths = array();
		foreach ($urls as $url) {
			// echo $template_path.'/'.$url;echo "<br/>";
 			if(file_exists($url)) {
				$paths[] = realpath($url);
			} elseif(file_exists($template_path.'/'.$url)) {
				// Not sure if you are necessary.. gonna keep for edge cases perhaps. Like includes.
				$paths[] = realpath($template_path.'/'.$url);
			} else {
				$paths[] = $url;
				// Catches case where it actually shows up on its own... relative to user's stylesheet.
				// TODO: come up with a way to differentiate between correct and fail, so we can throw warnings
			}
		}
		
		
		foreach ($paths as $i => $path) {
			// Accounting for the paths that already work but aren't 'correct' relative to c.w.d.
			if(file_exists($path)) {
				$paths[$i] = Filesystem::absoluteToRelative($this->attach('scarlet-'.$uid.'.css'), $path);				
			}
		}
		
		// Replace old paths with new paths
		foreach ($urls as $i => $url) {
			$combined = str_replace($url, $paths[$i], $combined);
		}

		
		$this->attach('scarlet-'.$uid.'.css', $combined, true);
		
		// echo $this->attachment('scarlet-'.$uid.'.css');
		// 
		// $from = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
		// $to = $this->attachment('scarlet-'.$uid.'.css');
		// // echo "FROM: $from";echo "<br/>";
		// // echo "TO: $to";echo "<hr/>";
		// $path = Filesystem::absoluteToRelative($from, $to);
		// // echo $path;
		$from = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		$to = $this->attach('scarlet-'.$uid.'.css');
		$path = Filesystem::absoluteToRelative($from, $to);
		
		$out = '<link rel="stylesheet" href="'.$path.'" type="text/css" media="screen" title="Scarlet" charset="utf-8" />';
		
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
	
	private function _get_urls($file) {
		// http://nadeausoftware.com/articles/2008/01/php_tip_how_extract_urls_css_file
		$urls = array( );

		$url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
		$urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
		$pattern         = '/(' .
			 '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
			'|(@import\s*'      . $urlfunc_pattern . ')'      .
			'|('                . $urlfunc_pattern . ')'      .  ')/iu';
		if ( !preg_match_all( $pattern, $file, $matches ) )
			return $urls;

		// @import '...'
		// @import "..."
		foreach ( $matches[3] as $match )
			if ( !empty($match) )
				$urls[] = 
					preg_replace( '/\\\\(.)/u', '\\1', $match );

		// @import url(...)
		// @import url('...')
		// @import url("...")
		foreach ( $matches[7] as $match )
			if ( !empty($match) )
				$urls[] = 
					preg_replace( '/\\\\(.)/u', '\\1', $match );

		// url(...)
		// url('...')
		// url("...")
		foreach ( $matches[11] as $match )
			if ( !empty($match) )
				$urls[] = 
					preg_replace( '/\\\\(.)/u', '\\1', $match );

		return $urls;
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