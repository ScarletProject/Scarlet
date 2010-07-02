<?php

/** 
* Short Description
*
* Long Description
* @package Cufon
* @author Matt Mueller
*/

class Cufon 
{
	private $text;
	private $font;
	function __construct(Tag $T)
	{
		$args = $T->defaults('text', 'font');
		extract($args, EXTR_OVERWRITE);
		
		$this->text = $text;
		$this->font = $font;
		
		// if($font) {
		// 	$font = dirname(__FILE__).'/Fonts/'.$font.'.js';
		// }

		// Unable to use this because it requires underlying software - fontforge
		// $font = Cufon_Generator::generate('Cochin.ttc', $options);
		$font_file = dirname(__FILE__).'/Fonts/'.$font.'.js';

		@ob_clean();
		ob_start();
		include('cufon.custom.php');
		$customFile = ob_get_contents();
		@ob_end_clean();
		file_put_contents(dirname(__FILE__).'/cufon.custom.js', $customFile);
		
		$T->assert('jquery', 'cufon.js','/Fonts/'.$font.'.js', 'cufon.custom.js');
		$T->wrap('span');
	}
	
	function __tostring(Tag $T) {
		return $this->text;
	}
}


?>