<?php

/**
 * adds pictures from your google picasa account to a data object for random retrieval.
 *@author nicolaas[at]sunnysideup.co.nz
 * example link: https://picasaweb.google.com/data/feed/api/user/xxx
 * example link: https://picasaweb.google.com/data/feed/api/user/nfrancken/albumid/5742357499437955281xxxx/
 * large image: https://lh4.googleusercontent.com/-zs8sog5SG0U/T7DzuF3Lw9I/AAAAAAAAP4E/rPZiGlVL_eY/s2500/IMG_1715.JPG
 * (Note 2500 as the width!)
 *
 *
 **/

class PicasaRandomImage extends DataObject {

	static $db = array(
		"URL" => "Text",
	);


	/**
	 * google username e.g. firstname.lastname (if your email address is firstname.lastname@google.com)
	 * @var string
	 */
	protected static $google_username = '';
		static  function set_google_username($v) {self::$google_username = $v;}
		static  function get_google_username() {return self::$google_username;}

	/**
	 * set to 30 to take one in thirty albums
	 * set to 1 to take all
	 * @var Int
	 */
	protected static $number_of_folders = 10;
		static  function set_number_of_folders($i) {self::$number_of_folders = $i;}
		static  function get_number_of_folders() {return self::$number_of_folders;}

	/**
	 * set to 30 to take one in thirty pictures
	 * set to 1 to add all
	 * @var Int
	 */
	protected static $number_of_images_per_folder = 2;
		static  function set_number_of_images_per_folder($i) {self::$number_of_images_per_folder = $i;}
		static  function get_number_of_images_per_folder() {return self::$number_of_images_per_folder;}

	public static function get_random_image($width){
		$objects = DataObject::get("PicasaRandomImage", "", "RAND()");
		if($objects && $obj = $objects->First()) {
			$obj->URL = str_replace('/s72/', '/s'.$width.'/', $obj->URL);
			return $obj;
		}
	}

	public function requireDefaultRecords(){
		parent::requireDefaultRecords();
		if(isset($_GET["updatepicassapics"])) {
			$albums = $this->getAlbums(PicasaRandomImage::$google_username);
			if(is_array($albums) && count($albums)) {
				$selectedAlbums = array_rand($albums, (count($albums <= self::get_number_of_folders()) ? self::get_number_of_folders() : count($albums)));
				if(!is_array($selectedAlbums)) {
					$selectedAlbums = array($selectedAlbums);
				}
				foreach($selectedAlbums as $albumKey) {
					$albumTitle = $albums[$albumKey];
					//google wants only the letters and numbers in the url
					$albumTitle = ereg_replace("[^A-Za-z0-9]", "", $albumTitle);
					//get the list of pictures from the album
					$pictures = $this->showAlbumContent(PicasaRandomImage::$google_username, $albumTitle);
					if(is_array($pictures) && count($pictures)) {
						$selectedPictures = array_rand($pictures, (count($pictures <= self::get_number_of_images_per_folder()) ? self::get_number_of_images_per_folder() : count($pictures)));
						//get a random picture from the album
						if(!is_array($selectedPictures)){
							$selectedPictures = array($selectedPictures);
						}
						foreach($selectedPictures as $pictureKey) {
							$picture = $pictures[$pictureKey];
							$url = $picture["src"];
							if(!DataObject::get_one("PicasaRandomImage", "PicasaRandomImage.URL = '$url'")) {
								$obj = new PicasaRandomImage();
								$obj->URL = $url;
								$obj->write();
								DB::alteration_message("adding picasa random image: ".$obj->URL."<img src=\"$url\" alt=\"\">", "created");
							}
						}
					}
				}
			}
		}
	}

	//GET THE REMOTE FILE INTO A VARIABLE
	protected function curlit($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$return=curl_exec ($ch);
		curl_close ($ch);
		return $return;
	}

	//THIS IS BASED ON http://blogoscoped.com/archive/2007-03-22-n84.html
	protected function getAlbums($userId) {
		$tmp = array();

		$url = 'http://picasaweb.google.com/data/feed/api/user/' . urlencode($userId) . '?kind=album';
		$xml = $this->curlit($url);
		$xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);
		$dom = new domdocument;
		if($xml) {
			$dom->loadXml($xml);

			$xpath = new domxpath($dom);
			$nodes = $xpath->query('//entry');

			foreach ($nodes as $node) {
				$rights = $xpath->query('rights', $node)->item(0)->textContent;
				if($rights == "public") {
					$tmp[] = $xpath->query('title', $node)->item(0)->textContent;
				}
			}
		}
		return $tmp;
	}

	//THIS IS BASED ON http://blogoscoped.com/archive/2007-03-22-n84.html
	protected function showAlbumContent($userId, $albumName) {
		$tmp = array();
		$url = 'http://picasaweb.google.com/data/feed/api/user/' . urlencode($userId) . '/album/'.$albumName."/";
		$xml = $this->curlit($url);
		$xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);
		if($xml == "No album found.") {
			DB::alteration_message("$albumName NOT FOUND", "deleted");
			return $tmp;
		}
		$dom = new domdocument;
		if($xml){
			$dom->loadXml($xml);
			$xpath = new domxpath($dom);
			$nodes = $xpath->query('//entry');
			foreach ($nodes as $node) {
				$tmp[]['src'] = $xpath->query('.//media:thumbnail/@url', $node)->item(0)->textContent;
			}
		}
		return $tmp;
	}


}


class PicasaRandomImage_Controller extends ContentController{

	static $allowed_actions = array(
		"one" => "ADMIN",
		"mylist" => "ADMIN"
	);

	function one($request){
		$width = $request->Param("ID");
		$image = PicasaRandomImage::get_random_image($width);
		if($image) {
			return $image->URL;
		}
	}

	function mylist($request){
		echo "\$array = array(";
		$width = $request->Param("ID");
		if(!$width) {
			$width = 2400;
		}
		$objects = DataObject::get("PicasaRandomImage");
		if($objects) {
			foreach($objects as $obj) {
				$obj->URL = str_replace('/s72/', '/s'.$width.'/', $obj->URL);
				echo "\r\n";
				echo "\t'".$obj->URL."'";
				if(!$obj->Last()) {
					echo ",";
				}
			}
		}
		echo "\r\n);";
	}

}
