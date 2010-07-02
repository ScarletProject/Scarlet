<?php
/**
* Forms
*/

class forms
{
	private static $javascript = '/Scarlet/Library/ui/forms/forms.js';
	private static $css = '/Scarlet/Library/ui/forms/forms.css';
	
	public static function form() {
		$args = Librarian::args(func_get_args());
		$args = Librarian::defaults($args, 'action', 'method');
		$args = Librarian::attributes($args, 'action', array( 'method'=>'post'));
		Librarian::assert(self::$css);
				
		$out = '<form action="'.$args['action'].'" method="'.$args['method'].'" accept-charset="utf-8">';

		return Librarian::start_enclose($out, 'forms form');
	}
	public static function endform() {
		return Librarian::end_enclose('</form>');
	}
	
	public static function text(Tag $T) {
		$args = $T->args(
			'value', 
			array('width'=>'200px'), 
			'maxlength', 
			array('enclose_type'=>'div')
		);
		
		$T->assert(self::$css, 'jquery', self::$javascript);
			
		$out = '<input type = "text" 
			value = "'.$args['value'].'" 
			style="width:'.$args['width'].';"
			maxlength="'.$args['maxlength'].'"
		/>';
		
		$T->addClass('round');
		$T->width($args['width']);

		return $out;
	}
	
	public static function submit() {
		$args = Librarian::args(func_get_args());
		$args = Librarian::defaults($args, 'value', 'width');
		$args = Librarian::attributes($args, array( 'width'=>'200px' ),'value');
		Librarian::assert(self::$css);
		
		$out = '<input type = "submit" 
			value = "'.$args['value'].'" 
			style = "'.$args['width'].';"
		/>';
		
		return $out;
	}
	
	private static function toString() {
		return get_class(self);
	}
}



?>