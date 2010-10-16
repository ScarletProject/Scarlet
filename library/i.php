<?php

/** 
* Include Tag
*
* Long Description
* @package i
* @author Matt Mueller
*/

class i extends Tag
{
	
	function setup()
	{
		$args = $this->defaults('file', 'wrap');
		if($this->arg('wrap')) {
			$this->wrap($this->arg('wrap'));
		} else {
			$this->wrap(false);
		}
	}
	
	function show() {
		$suffix = end(explode('.', $this->arg('file')));
		switch ($suffix) {
			case 'css':
				$this->stylesheet('/'.getcwd().'/'.$this->arg('file'));
				return '';
				break;
			case 'js':
				$this->script('/'.getcwd().'/'.$this->arg('file'));
				return '';
				break;
			default:
				return S($this->arg('file'))->fetch();
				break;
		}		
	}
}


?>