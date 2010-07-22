<?php header('Cache-Control: no-store, no-cache, must-revalidate');
// Single include file and you can start writing Scarlet!!!
require_once(dirname(__FILE__).'/classes/Template.php');

$content = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);

$pattern = '/<\?php([\d\D]*[require|include][\d\D]*AutoCompile.php[^;]*;)/i';
$replacement = '<?php /* $1 */';
$content = preg_replace($pattern, $replacement, $content);

$template = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];

// Clean out directory 
if(is_dir(dirname($template).'/.scarlet/')) {
	exec('rm -r '.dirname($template).'/.scarlet/');
}

// Make directory
mkdir(dirname($template).'/.scarlet/');

// Add directory as a path
S()->path('attachments', dirname($template).'/.scarlet');

$content = S($template)->parse($content, true);

eval('?>'.$content);

exit(0);
?>