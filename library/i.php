<?php

/** 
* Include Tag
*
* Long Description
* @package i
* @author Matt Mueller
*/

class i 
{
	
	function __construct(Tag $T)
	{
		$args = $T->defaults('template');
		extract($args, EXTR_OVERWRITE);
		
		if(end(explode('.', $template)) != 'tpl') {
			$T->error('Not a .tpl file!', __CLASS__,__FUNCTION__,__LINE__);
		} elseif(!file_exists($template)) {
			$T->error('File doesn\'t exist! '.$template, __CLASS__,__FUNCTION__,__LINE__);
		}
	}
	
	function __tostring(Tag $T) {
		return $T->args('content');
		
	}
}


?>