<?php

/** 
* Short Description
*
* Long Description
* @package Row
* @author Matt Mueller
*/

class Grid_Row 
{
	private $Tag;

	function __construct(Tag $T)
	{
		$T->leftWrap();
	}
	
	public function socialize(Tag $T) {

	}
	
	function __tostring() {
		return '';
	}
}

class Grid_EndRow 
{
	
	function __construct(Tag $T)
	{
		$T->rightWrap();
	}
	
	function __tostring(Tag $T) {
		return '';
	}
}

?>