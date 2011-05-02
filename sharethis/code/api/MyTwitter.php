<?php

/**
 *@author romain [at] sunnys side up .co.nz
 *
 **/

class MyTwitter extends RestfulService {

	static function last_statuses($username, $count = null) {
		$url = "http://twitter.com/statuses/user_timeline/$username.xml";
		if($count) {
			$url = HTTP::setGetVar('count', $count, $url);
		}
		$content = file_get_contents($url);
		if($content) {
			return new SimpleXMLElement($content);
		}
	}
}
