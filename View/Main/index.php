<?php require_once(dirname(__FILE__).'/../../Scarlet.php');

	$action = isset($_GET['action']) ? $_GET['action'] : 'main';	
	function_exists($action) ? $action() : error();
	exit(0);

	function main() {
		echo S('master.tpl')->projectPath('..');
		
	}
		
	function error() {
		
	}

?>