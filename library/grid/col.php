<?php

/** 
* Short Description
*
* Long Description
* @package Grid_Col
* @author Matt Mueller
*/

class Grid_Col 
{
	
	function __construct(Tag $T)
	{
		$args = $T->defaults(
			array('col_width' => 12)
		);

		extract($args, EXTR_OVERWRITE);

		// $parts = explode('-', $col_width);
		// $begin = $parts[0];
		// $end = $parts[1];
		// 
		// // echo $begin;echo "<br/>";echo $end;echo "<br/>";
		// 
		// $span = $end - $begin + 1;
		// 
		$T->leftWrap();
		
		// $T->style('background-color', 'lightgray');
		$T->addClass('col_'.$col_width);
		
		// if($T->prev()->is('Grid:Col')) {
		// 	
		// } else {
		// 	$filler_span = $begin - 1;
		// 	$FillerCol = new Tag('Grid:Col', array("1-$filler_span"));
		// 	$FillerColEnd = new Tag('Grid:EndCol', array("1-$filler_span"));
		// 	$T->prev($FillerCol.'&nbsp;'.$FillerColEnd);
		// }
	}
	
	function socialize(Tag $T) {
		
	}
	
	function __tostring()
	{
		return '';
	}
}

class Grid_EndCol 
{
	
	function __construct(Tag $T)
	{
		$T->rightWrap();
	}
	
	function __tostring()
	{
		return '';
	}
}


?>