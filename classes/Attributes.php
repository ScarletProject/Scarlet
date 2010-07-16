<?php

// Contains global attributes that can be applied to all Tags

class Attribute {

	public static function style($value, $Tag) {
		if(is_array($value)) {
			$Tag->style($value);
		}
	}
	
	public static function attr($value, $Tag) {
		if(is_array($value)) {
			$Tag->attr($value);
		}
	}

	public static function attr_class($value, $Tag) {
		$Tag->addClass($value);
	}
	
	public static function id($value, $Tag) {
		$Tag->id($value);
	}
	
	public static function rounded($value, $Tag) {
		$Tag->addClass('rounded');
		$Tag->give('css:rounded.css', 'roundness', $value);
	}
	
	public static function width($value, $Tag) {
		$Tag->width($value);
	}
	
	public static function height($value, $Tag) {
		$Tag->height($value);
	}
	
	public static function theme($value, $Tag) {
		// Temporarily include the new library
		if(defined('SCARLET_PROJECT_THEME_DIR')) {
			S()->library(SCARLET_PROJECT_THEME_DIR.'/'.$value);
		}
	}
}

?>