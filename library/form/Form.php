<?php

/** 
* Short Description
*
* Long Description
* @package Form
* @author Matt Mueller
*/

class Form extends Tag
{
	public function __construct($init) {
		Tag::__construct($init);

		$this->wrap('form');
		
		
	}
		
	public function __tostring() {
		print_r($this->path('attachment'));
		return 'FROM FORM!';
	}
}

/** 
* Short Description
*
* Long Description
* @package EndForm
* @author Matt Mueller
*/

class EndForm 
{
	
	function __construct()
	{
		
	}
	
	public function __tostring() {
		return htmlspecialchars('</form>');
	}
}


?>