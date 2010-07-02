<?php
// Require the class loader
if(!class_exists('ClassLoader')) {
	require_once dirname(__FILE__).'/classes/ClassLoader.php';
} 

// Require Tag
if(!class_exists('Tag')) {
	require_once dirname(__FILE__).'/classes/Tag.php';
}

define('SCARLET_DIR', realpath(dirname(__FILE__)));
define('SCARLET_LIBRARY_DIR', SCARLET_DIR.'/library');

// Universal loader
function loader() {
	if(!isset($GLOBALS['loader'])) {
		// Create an Autoload Object
		$GLOBALS['loader'] = new ClassLoader;
		return $GLOBALS['loader'];
	} else {
		return $GLOBALS['loader'];
	}
}

// Load the default library
loader()->library(SCARLET_LIBRARY_DIR);

function S($namespace, $library = null) {
	if($namespace[0] == '/') {
		$namespace = explode(':', substr($namespace,1));
		$namespace[count($namespace)-1] = 'End'.$namespace[count($namespace)-1];
		$namespace = implode(':',$namespace);
	}
	
	if(isset($library)) {
		loader()->library($library);
	}
	
	// Load the class
	loader()->add($namespace)->register();
	
	$class = str_replace(':','_',$namespace);
	
	// Params to be sent to Tag
	$tagParams = array();
	$tagParams['namespace'] = $namespace;
	$tagParams['args'] = array();
	
	// Creating the tag
	return new $class($tagParams);

}

?>