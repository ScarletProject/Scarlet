<?php

/** 
* Short Description
*
* Long Description
* @package video_youtube
* @author Matt Mueller
*/

class video_youtube extends tag
{
	private $video_id;
	
	function init() {
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
	}
	
	function tostring() {
		if(!isset($this->video_id)) return '';
		
		 return '<object width="640" height="385"><param name="movie" value="http://www.youtube.com/v/'.$this->video_id.'?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$this->video_id.'?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="385"></embed></object>';
		
	}
}



?>