<?php

/** 
* Short Description
*
* Long Description
* @package video_youtube
* @author Matt Mueller
*/

// Woah-fully Incomplete.

class video_youtube_chromeless extends tag
{
	
	function init() {
		$this->defaults('query = Macaw birds in the Amazon jungle');
		$query = urlencode($this->arg('query'));
		$json = file_get_contents('http://gdata.youtube.com/feeds/api/videos?q='.$query.'&max-results=1&v=2&alt=jsonc');
		
		$video = json_decode($json);
		$video = current($video->data->items);

		$this->video_id = $video->id;
		
		$this->give('chromeless.js', 'videoid', $this->video_id);

		$this->width(480);
		$this->height(295);
	}
	
	function tostring() {
		 return '<object width="480" height="295" type="application/x-shockwave-flash" id="ytPlayer" data="http://www.youtube.com/apiplayer?&amp;enablejsapi=1&amp;playerapiid=player1&amp;id"><param name="allowScriptAccess" value="always"></object>';
		
	}
}



?>