<?php 
// Single include file and you can start writing Scarlet!!!
require_once(dirname(__FILE__).'/classes/Template.php');

$content = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);

$pattern = '/<\?php([\d\D]*require[\d\D]*AutoCompile.php[^;]*;)/i';
$replacement = '<?php /* $1 */';
$content = preg_replace($pattern, $replacement, $content);

$template = new Template($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);

$content = $template->parse($content, true);

eval('?>'.$content);

exit(0);
?>