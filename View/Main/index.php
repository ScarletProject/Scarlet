<?php require_once(dirname(__FILE__).'/../../Scarlet.php');

	$action = isset($_GET['action']) ? $_GET['action'] : 'main';	
	function_exists($action) ? $action() : error();
	exit(0);

	function main() {
		$args = array();
		$args['hello'] = "Viva la Vida";
		$args['width'] = "120px";
		
		extract($args, EXTR_OVERWRITE);
		
		$content = S('master.tpl')->projectPath('..')->fetch();
		
		eval('?>'.$content);
	}
		
	function error() {
		
	}

?>