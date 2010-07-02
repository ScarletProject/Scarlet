<?php

/** 
* Just a basic slideshow
*
* Long Description
* @package Basic
* @author Matt Mueller
*/

class Slideshow extends Tag
{

	function init() {
		
		$this->defaults('slides');
		
		$this->addClass('slideshow');
		// Assert requirements
		$this->script('jquery', 'cycle', 'basic.js');
		
		$this->wrap();
		$this->style('border', '1px solid #555');
	}
	

	public function tostring() {
		$out = '';		
		
		// Build slideshow layout
		foreach ($this->args('slides') as $i => $slide) {

			if($i > 1) {
				$slide->style('display', 'none');
			}
			$out .= $slide;
		}

		
		return $out;
	}

}

?>