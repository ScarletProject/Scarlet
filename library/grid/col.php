<?php

/** 
* Short Description
*
* Long Description
* @package Grid_Col
* @author Matt Mueller
*/

class Grid_Col extends Tag
{
	
	function setup()
	{
		$this->defaults('length = 12');

		$this->wrap(true, false);
		$this->addClass('col-'.$this->arg('length'));
		
		
		// $this->style('background-color', 'lightgray');
		
		// if($T->prev()->is('Grid:Col')) {
		// 	
		// } else {
		// 	$filler_span = $begin - 1;
		// 	$FillerCol = new Tag('Grid:Col', array("1-$filler_span"));
		// 	$FillerColEnd = new Tag('Grid:EndCol', array("1-$filler_span"));
		// 	$T->prev($FillerCol.'&nbsp;'.$FillerColEnd);
		// }
	}

}

class Grid_EndCol extends Tag
{
	function setup()
	{
		$this->wrap(false, true);
	}
}


?>