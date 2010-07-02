<?php
/** 
* Short Description
*
* Long Description
* @package Grid
* @author Matt Mueller
*/

class Grid 
{

	function __construct(Tag $T)
	{
		$args = $T->defaults(
			array('num_cols' => 12),
			array('col_width' => 60),
			array('gutter_width' => 20)
		);
		extract($args, EXTR_OVERWRITE);
		
		@ob_clean();
		ob_start();
		include('grid.custom.php');
		$customFile = ob_get_contents();
		@ob_end_clean();
		
		// This might work.. its like a temporary playground before it gets copied
		// to appropriate place.
		file_put_contents(dirname(__FILE__).'/'.'grid.custom.css', $customFile);

		$T->assert('grid.custom.css');
		$T->leftWrap();
	}
	
	public function socialize(Tag $T) {

	}

	function __tostring(Tag $T) {
		return '';
	}

	
}

/** 
* Short Description
*
* Long Description
* @package EndGrid
* @author Matt Mueller
*/

class EndGrid 
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