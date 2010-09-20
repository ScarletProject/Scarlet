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

	function init()
	{
		$this->defaults('numCols = 12', 'colWidth = 70', 'gutterWidth = 20');
		
		$this->arg('numCols', rtrim($this->arg('numCols'), 'px'));
		$this->arg('colWidth', rtrim($this->arg('colWidth'), 'px'));
		$this->arg('gutterWidth', rtrim($this->arg('gutterWidth'), 'px'));
				
		extract($this->arg(), EXTR_OVERWRITE);
		
		@ob_clean();
		ob_start();
		include('grid.custom.php');
		$customFile = ob_get_contents();
		@ob_end_clean();

		$this->attach('grid.css', $customFile, true);
		$this->stylesheet($this->attach('grid.css'));
		$this->wrap(true, false);
		
		$this->data('grid-num-cols', $this->arg('num_cols'));
		$this->data('grid-col-width', $this->arg('col_width'));
		$this->data('grid-gutter-width', $this->arg('gutter_width'));
		
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
	function init()
	{
		$this->wrap(false, true);
	}
}




?>