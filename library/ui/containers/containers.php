<?php

/**
* Containers
*/
class Containers
{
	public static $javascript = '/Jeeves/Libraries/ui/containers/containers.js';
	public static $css = '/Jeeves/Libraries/ui/containers/containers.css';

	public static function roundedbox() {
		// $args = Librarian::args(func_get_args());
		// 		$args = Librarian::defaults($args, 'header', 'width', 'border', 'background');
		// 		$args = Librarian::attributes(
		// 			$args, 'header', array('width'=>'500px'), 
		// 			array('border'=>'#79CDFF'), array('background'=>'#D3F2FF')
		// 		);
		// 		
		// 		Librarian::assert(self::$css);
		// 				
		// 		$header = '<div class="header rounded" 
		// 			style="background-color:'.$args['border'].';
		// 		">'.$args['header'].'</div>';
		// 		
		// 		$container = '<div class="container rounded"
		// 			style="border-color:'.$args['border'].'; background-color:'.$args['background'].';
		// 		">';
		// 		
		// 		return Librarian::start_enclose($header.$container, 'containers roundedbox', array('width' => $args['width']));
		return "WAHOOS";
	}
	public static function endroundedbox() {
		return Librarian::end_enclose('</div>');
	}
	
	public static function listview(Bone $B) {
		$args = $B->params('data','width', array('background'=>'#393939'));


		$B->assert(self::$css);
		
		// if(empty($data))
		// 	Jeeves::error("No data to place in table!",__CLASS__,__FUNCTION__,__LINE__);
		
		$out = '<table border = 0 >';
		foreach ($args['data'] as $data) {
			$out .= '<tr><td>';
				$out .= $data;
			$out .= '</td></tr>';
		}
		$out .= '</table>';

		$B->style('background-color', $args['background']);
		$B->width($args['width']);

		return $out;
	}
}



?>