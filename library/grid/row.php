<?php

/** 
* Short Description
*
* Long Description
* @package Row
* @author Matt Mueller
*/

class Grid_Row extends Tag
{
	function init()
	{
		$this->wrap(true, false);
	}
}

class Grid_EndRow extends Tag
{
	function init()
	{
		$this->wrap(false, true);
	}
}

?>