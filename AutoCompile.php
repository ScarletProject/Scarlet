<?php

require_once(dirname(__FILE__).'/Scarlet.php');
$template = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];

if(file_exists($template)) {

	S()->init(dirname($template))->stage('dev');
	
	$compiledFile;
	$compiledMD5;
	if(S($template)->isCompiled()) {
		$compiledFile = S($template)->findCompiledFile();
		$file = file_get_contents($compiledFile);
		$compiledMD5 = md5($file);
	}

	$content = S($template)->fetch();


	$file = basename(__FILE__);
	$pattern = '/(?:require|include)(?:_once)?(?:[\s]+)?+[(]?(?:[\s]+)?[\'"][\w.\/]+Autocompile2.php[\'"](?:[\s]+)?[)]?(?:[\s]+)?;/i';
	$replacement = '';
	
	$content = preg_replace($pattern, $replacement, $content);
	
	$contentMD5 = md5($content);
	
	if(isset($compiledFile) && $contentMD5 == $compiledMD5) {
		S()->stage('live');
		echo "<!-- Retrieved from compiled directory -->";
		include_once $compiledFile;
		exit(0);
	} else {
		eval('?>'.$content);
		S($template)->compile($content);
		exit(0);
	}

}


?>