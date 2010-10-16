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
	
	function setup() {
		$this->defaults('video');
		
		// If an video id is given, just use that.
		if($this->arg('id')) {
			$this->video_id = $this->arg('id');
			return;
		} elseif($this->arg('video')) {
			$query = urlencode($this->arg('video'));
			$json = file_get_contents('http://gdata.youtube.com/feeds/api/videos?q='.$query.'&max-results=1&v=2&alt=jsonc');
		
			$video = json_decode($json);
			$video = current($video->data->items);

			$this->video_id = $video->id;
		}
		
		$this->give('chromeless.js', 'videoid', $this->video_id);

		$this->width(640);
		$this->height(385);
	}
	
	function show() {
		 return '<object width="640" height="385" type="application/x-shockwave-flash" id="ytPlayer" data="http://www.youtube.com/apiplayer?&amp;enablejsapi=1&amp;playerapiid=player1&amp;id"><param name="allowScriptAccess" value="always"></object>';
		
	}
}



?>