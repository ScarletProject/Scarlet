<?php

/** 
* Short Description
*
* Long Description
* @package Button
* @author Matt Mueller
*/

class Button extends Tag
{
	
	function setup() {

		$this->defaults('value = Button', 'link = #');
		
		$this->attach('highlight', 'images/colors.png');
		$this->give('button.css', 'url', $this->attach('highlight'));
				
		$this->wrap('a');
		$this->attr('href', $this->arg('link'));
		$this->addClass($this->arg('color'));
		
		// Handle rounded arguments
		if($this->args('rounded') === true) {
			$this->give('css:rounded/rounded.css', 'roundness', '5px');
		} else if($this->args('rounded')) {
			$this->give('css:rounded/rounded.css', 'roundness', $this->args('rounded'));
		}
		
		if($this->args('rounded-bottom')) {
			$this->give('css:rounded/roundedBottom.css', 'roundness', $this->args('rounded-bottom'));
		}
		
		if($this->args('rounded-top')) {
			$this->give('css:rounded/roundedTop.css', 'roundness', $this->args('rounded-top'));
		}
		
		if($this->args('rounded-left')) {
			$this->give('css:rounded/roundedLeft.css', 'roundness', $this->args('rounded-left'));
		}
		
		if($this->args('rounded-right')) {
			$this->give('css:rounded/roundedRight.css', 'roundness', $this->args('rounded-right'));
		}
	}
	
	function show() {
		return $this->arg('value');
	}
}


?>