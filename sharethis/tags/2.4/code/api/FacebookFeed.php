<?php
/**
 * STOLEN FROM: http://www.acornartwork.com/blog/2010/04/19/tutorial-facebook-rss-feed-parser-in-pure-php/
 * EXAMPLE:
 *		//Run the function with the url and a number as arguments
 *		$fb = new TheFaceBook_communicator();
 *		$dos = $fb->fetchFBFeed('http://facebook.com/feeds/status.php?id=xxxxxx&viewer=xxxxxx&key=xxxxx&format=rss20', 3);
 *		//Print Facebook status updates
 *		echo '<ul class="fb-updates">';
 *			 foreach ($dos as $do) {
 *					echo '<li>';
 *					echo '<span class="update">' .$do->Description. '</span>';
 *					echo '<span class="date">' .$do->Date. '</span>';
 *					echo '<span class="link"><a href="' .$do->Link. '">more</a></span>';
 *					echo '</li>';
 *			 }
 *		echo '</ul>';
 *
 *
 *
 *
 **/

class FacebookFeed_Item extends DataObject{

	static $db = array(
		"KeepOnTop" => "Boolean",
		"Hide" => "Boolean",
		"UID" => "varchar(32)",
		"Title" => "varchar(255)",
		"Author" => "Varchar(244)",
		"Description" => "HTMLText",
		"Link" => "Varchar(244)",
		"Date" => "Date"
	);

	static $indexes = array(
		"UID" => true
	);

	static $default_sort = "\"KeepOnTop\" DESC, \"Date\" DESC";

}


class FacebookFeed_Item_Communicator extends RestfulServer {

	/**
	 * @param String: https://www.facebook.com/feeds/page.php?format=rss20&id=xxxxxxx
	 * @param Int $maxnumber - items retrieved
	 * @param String
	 * @returns DataObjectSet
	 **/

	function fetchFBFeed($url, $maxnumber = 1, $timeFormat = 'Y-m-d') {
	/* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */
		ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
		$updates = simplexml_load_file($url);  //Load feed with simplexml
		foreach ( $updates->channel->item as $fbUpdate ) {
			if ($maxnumber == 0) {
				break;
			}
			else {
				$guid = $fbUpdate->guid;
				$uid = substr($guid, -32);
				if(DB::query("SELECT COUNT(\"ID\") FROM \"FacebookFeed_Item\" WHERE \"UID\" = '$uid';")->value() == 0) {
					$desc = $fbUpdate->description;
					//Add www.facebook.com to hyperlinks
					//$desc = urldecode(urldecode(str_replace('href="http://www.facebook.com/l.php?u=', 'href="', $desc)));
					//Converts UTF-8 into ISO-8859-1 to solve special symbols issues
					$desc = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
					$desc = $this->stripUnsafe($desc);
					//Get status update time
					$pubDate = strtotime($fbUpdate->pubDate);
					$convertedDate = gmdate($timeFormat, $pubDate);  //Customize this to your liking
					//Get link to update
					//Store values in array
					$FacebookFeed_Item = new FacebookFeed_Item();
					$FacebookFeed_Item->UID = (string) $uid;
					$FacebookFeed_Item->Title = (string) $fbUpdate->title;
					$FacebookFeed_Item->Date = $convertedDate;
					$FacebookFeed_Item->Author = (string) $fbUpdate->author;
					$FacebookFeed_Item->Link = (string) $fbUpdate->link;
					$FacebookFeed_Item->Description = $this->stripUnsafe((string) $desc);
					$FacebookFeed_Item->write();
					$maxnumber--;
				}
			}
		}
	}

	function stripUnsafe($string) {
    // Unsafe HTML tags that members may abuse
		$unsafe=array(
			'/onmouseover="(.*?)"/is',
			'/onclick="(.*?)"/is',
			'/style="(.*?)"/is',
			'/target="(.*?)"/is',
			'/onunload="(.*?)"/is',
			'/rel="(.*?)"/is',
			'/<a(.*?)>/is',
			'/<\/a>/is'
		);
		$string= preg_replace($unsafe, " ", $string);
		return $string;
	}


}

