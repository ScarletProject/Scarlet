<?php

/** 
* Short Description
*
* Long Description
* @package Search_Live
* @author Matt Mueller
*/

class Search_Live 
{
	private $Tag;
	
	public $value;
	public $width;
	public $controller;
	public $action;
	
	public function __construct(Tag $T)
	{
		$this->Tag = $T;
		$args = $T->args('value', 'action', array('width' => '400px'), 'controller');
		extract($args, EXTR_OVERWRITE);
		
		$this->value = $value;
		$this->width = $width;
		$this->controller = $controller;
		// Librarian::library('ui:forms', 'ui:containers');

		$T->assert('search.js', 'search.css', 'ui:containers:containers.css');
		
		// Format action
		$parts = explode(' ', $args['action']);
		if(count($parts > 1))
			$action = implode('_', $parts);
		else
			$action = $parts;
		
		// $action = 'search_'.$action;
		$this->action = $action;

		$T->give(array(
			'controller' => $args['controller'],
			'action' => $action
		));
		
		
	}
	
	public function __tostring() {
		$out = new Tag('Form:Text', array($this->value, $this->width));
		$out .= '<div class="results"></div>';
		return $out;
	}
}

?>