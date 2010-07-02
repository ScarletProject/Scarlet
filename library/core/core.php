<?php
/**
* Core
*/

class Core {
	


	public static function i($file) {
		@ob_clean();
		ob_start();
		require_once( $file );
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	public static function A() {
		return parent::args(func_get_args());
		
	}
	
	public static function namespace_cache() {
		@ob_clean();
		ob_start();
		echo '<hr/><strong style="color:#000000">Namespace Cache:</strong>';
		echo '<pre style="color:#000000">';
		print_r(Jeeves::$namespace_cache);
		echo '</pre><hr/>';
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public static function print_r($args) {
		$args = Librarian::params($args, 'array');
		
		@ob_clean();
		ob_start();
		echo '<hr/><strong>$arr:</strong>';
		echo '<pre>';
		print_r($args['array']);
		echo '</pre><hr/>';
		$content = ob_get_contents();
		ob_end_clean();
		

		
		
		return $content;
	}
	
	public static function css(Tag $T) {
		$T->wrap(false);
		$stylesheets = array_unique(Template::stylesheets());

		$out = array();
		foreach ($stylesheets as $css) {
			$out[] = '<link rel="stylesheet" href="'.$css.'" type="text/css" media="screen" charset="utf-8">';
		}
		$out = implode("", $out);
		
		// Clear stylesheets
		Template::clear_stylesheets($T);
		
		return $out;
	}
	
	public static function javascript(Tag $T) {
		$T->wrap(false);
		$javascripts = array_unique(Template::scripts());

		$out = array();

		foreach ($javascripts as $js) {
			$out[] = '<script src="'.$js.'" type="text/javascript" charset="utf-8"></script>';
		}
		$out = implode("", $out);
		
		Template::clear_scripts($T);

		return $out;
	}
	
	public static function script($js) {
		/*
			TODO Allow scripts to be manually placed without repeating scripts..
		*/
		// $index = array_search($js, Librarian::$javascripts);
		// if($index !== false)
		// 	unset(Librarian::$javascripts[$index]);
		return '<script src="'.$js.'" type="text/javascript" charset="utf-8"></script>';
	}
	
	public static function library() {
		$namespaces = func_get_args();
		foreach ($namespaces as $namespace) {
			Librarian::cache_namespace($namespace);
		}
	}
	
	public static function endlibrary() {		
		Librarian::clear_namespace_cache();
	}
	
	public static function lowercase(Tag $T) {

		// $T->wrap(false);
		$T->assert('ui:containers:containers.css','jquery','core.js');
		$T->give('test', 'wahoo');
		$T->give('blah', 'blz');
		
		$args = $T->args('string');
		
		return strtolower($args['string']);
	}
	
	public static function capitalize(Tag $T) {
		$T->wrap(false);
		$T->assert('core.css', 'javascript:jquery');
		
		$args = $T->args('string');
		return ucwords($args['string']);
	}
}

?>