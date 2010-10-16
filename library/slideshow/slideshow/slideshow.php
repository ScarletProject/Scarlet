<?php

/** 
* Just a basic slideshow
*
* Long Description
* @package Basic
* @author Matt Mueller
*/

class slideshow extends Tag
{

	function setup() {
		
		$this->defaults('slides');
		
		// Assert requirements
		$this->script('jquery', 'cycle.js', 'basic.js');
		
		$this->wrap();
		$this->style('border', '1px solid #555');
	}
	

	public function show() {
		$out = '';		
		
		// Build slideshow layout
		foreach ($this->args('slides') as $i => $slide) {
			$img = S('<img>')->attr(array(
				'src' => $slide
			));
			
			if($i > 0) {
				$img->style('display', 'none');
			}
			$out .= $img;
		}

		
		return $out;
	}

}

?>