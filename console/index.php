<?php require_once(dirname(__FILE__).'/../Scarlet.php');
	S()->init('.');

	$action = isset($_GET['action']) ? $_GET['action'] : 'main';	
	function_exists($action) ? $action() : error();
	exit(0);

	function main() {
		S('main.tpl')->show();
	}
		
	function render() {
		$text = $_POST['text'];
		$text = stripslashes($text);
		
		try {
			$content = S()->parse($text);
			
			$out = array();
			$out['content'] = $content;

			$assets = S()->getAssets();
			
			$css = array(); $javascript = array();
			
			$out['js'] = array();
			$out['css'] = array();
			
			foreach ($assets as $asset) {
				// Get webserver path.
				$root = $_SERVER['DOCUMENT_ROOT'];
				$root = explode('/', $root);
				$asset = explode('/', $asset);
				
				$asset = array_diff($asset, $root);
				$asset = '/'.implode('/', $asset);
				
				if(stristr($asset, '.js') !== false) {
					$out['js'][] = $asset;
				} elseif(stristr($asset, '.css') !== false) {
					$out['css'][] = $asset;
				}
			}
			
			$out['content'] = str_replace("\n\n", "<br />", $out['content']);
			$out['content'] = str_replace("\n", "", $out['content']);
			
			$out = json_encode($out);

			echo($out);
			
		} catch(Exception $e) {
			echo "";
		}
	}	
		
	function error() {
		
	}

?>