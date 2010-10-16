<?php

/** 
* Short Description
*
* Long Description
* @package Namespace
* @author Matt Mueller
*/

class Namespace extends Tag
{
	
	function setup() {
		$this->defaults('namespace');
		// S()->_add_to_namespace_cache($this->arg('namespace'));
				// 
				// $location = S()->location($this->arg('namespace'));
				// echo $location;echo "<br/>";
				// S()->library($location);
				// print_r(S()->library());
		// $T->add_namespace_to_cache($namespace);
	}
	
	function show() {
		return '';
	}
}

class EndNamespace extends Tag
{
	function setup() {
		$this->defaults('namespace');
				
		try {
			$location = S()->location($this->arg('namespace'));
			S()->removeLibrary($location);
		} catch (Exception $e) {  }
		// $T->remove_namespace_from_cache($namespace);
	}
	
	function show() {
		return '';
	}
}

?>