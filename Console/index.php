<?php require_once(dirname(__FILE__).'/../Scarlet.php');

	$action = isset($_GET['action']) ? $_GET['action'] : 'main';	
	function_exists($action) ? $action() : error();
	exit(0);

	function main() {
		S()->init('.');
		S('main.tpl')->show();
	}
		
	function render() {
		$text = $_POST['text'];
		$text = stripslashes($text);
		
		try {
			$content = S()->parse($text);
			
			$out = array();
			$out['content'] = $content;
			
			$stylesheets = S('<div>')->stylesheet();
			$stylesheets = array_keys($stylesheets);

			$scripts = S('<div>')->script();
			$scripts = array_keys($scripts);
			$out['assets'] = implode(" ", array_merge($stylesheets, $scripts));
			$out['assets'] = array();
			
			echo json_encode($out);
			
		} catch(Exception $e) {
			echo "";
		}
	}	
		
	function error() {
		
	}

?>