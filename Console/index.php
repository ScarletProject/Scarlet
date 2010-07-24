<?php require_once(dirname(__FILE__).'/../Scarlet.php');

	$action = isset($_GET['action']) ? $_GET['action'] : 'main';	
	function_exists($action) ? $action() : error();
	exit(0);

	function main() {
		$content = S('main.tpl')->projectPath('.')->fetch();
		
		eval("?>".$content);
	}
		
	function render() {
		$text = $_POST['text'];
		$text = stripslashes($text);
		S()->projectPath('.');
		
		try {
			$content = S('main.tpl')->parse($text, true);
			
			$out = array();
			$out['content'] = $content;
			
			$stylesheets = S('<div>')->stylesheet();
			$stylesheets = array_keys($stylesheets);

			$scripts = S('<div>')->script();
			$scripts = array_keys($scripts);
			$out['assets'] = implode(" ", array_merge($stylesheets, $scripts));
			
			echo json_encode($out);
			
		} catch(Exception $e) {
			echo "";
		}
	}	
		
	function error() {
		
	}

?>