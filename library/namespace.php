<?php

/** 
* Short Description
*
* Long Description
* @package Namespace
* @author Matt Mueller
*/

class Namespace 
{
	
	function __construct(Tag $T) {
		$args = $T->defaults('namespace');
		extract($args, EXTR_OVERWRITE);
		
		$T->add_namespace_to_cache($namespace);
	}
	
	function __tostring(Tag $T) {
		return '';
	}
}

class EndNamespace
{
	function __construct(Tag $T) {
		$args = $T->defaults('namespace');
		extract($args, EXTR_OVERWRITE);
				
		$T->remove_namespace_from_cache($namespace);
	}
	
	function __tostring(Tag $T) {
		return '';
	}
}

?>