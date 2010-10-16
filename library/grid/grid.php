<?php
/** 
* Short Description
*
* Long Description
* @package Grid
* @author Matt Mueller
*/

class Grid extends Tag
{

	function setup()
	{
		$this->defaults('width = 960','numCols = 12', 'gutterWidth = 20');
		extract($this->arg(), EXTR_OVERWRITE);
		
		if(!isset($colWidth)) {
			$colWidth = round(($width - $numCols*$gutterWidth) / $numCols);
		}
		
		$this->arg('numCols', rtrim($numCols, 'px'));
		$this->arg('colWidth', rtrim($colWidth, 'px'));
		$this->arg('gutterWidth', rtrim($gutterWidth, 'px'));
		
		extract($this->arg(), EXTR_OVERWRITE);		
		
		@ob_clean();
		ob_start();
		include('grid.custom.php');
		$customFile = ob_get_contents();
		@ob_end_clean();

		$this->attach('grid.css', $customFile, true);
		$this->stylesheet($this->attach('grid.css'));
		$this->wrap(true, false);
		
		$this->data('grid-numCols', $numCols);
		$this->data('grid-colWidth', $colWidth);
		$this->data('grid-gutterWidth', $gutterWidth);
		
	}
	
}

/** 
* Short Description
*
* Long Description
* @package EndGrid
* @author Matt Mueller
*/

class EndGrid extends Tag
{
	function setup()
	{
		$this->wrap(false, true);
	}
}




?>