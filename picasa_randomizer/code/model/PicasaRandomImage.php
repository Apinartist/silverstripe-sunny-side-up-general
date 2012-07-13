<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@PicasaRandomImage::set_google_username("tester")
 *@PicasaRandomImage::set_number_of_folders(3)
 *@PicasaRandomImage::set_number_of_images_per_folder(2)
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

	protected static $google_username = '';
		static  function set_google_username($v) {self::$google_username = $v;}
		static  function get_google_username() {return self::$google_username;}

	public static function get_random_image($width){
		$objects = DataObject::get("PicasaRandomImage", "", "RAND()");
		if($objects && $obj = $objects->First()) {
			$obj->URL = str_replace('/s72/', '/s'.$width.'/', $obj->URL);
			return $obj;
		}
	}

	public function requireDefaultRecords(){
		parent::requireDefaultRecords();
		$albums = $this->getAlbums(PicasaRandomImage::$google_username);
		for($i = 0; $i < 30; $i++) {
			//get a random album
			$album_title = $albums[array_rand($albums, 1)];
			//google wants only the letters and numbers in the url
			$album_title = ereg_replace("[^A-Za-z0-9]", "", $album_title);
			//get the list of pictures from the album
			$pictures = $this->showAlbumContent(PicasaRandomImage::$google_username, $album_title);
			//get a random picture from the album
			if(is_array($pictures)){
				$randomPic = $pictures[array_rand($pictures,1)]['src'];
				if(!DataObject::get("PicasaRandomImage", "URL <> '$randomPic'")) {
					$obj = new PicasaRandomImage();
					$obj->URL = $randomPic;
					$obj->write();
					DB::alteration_message("adding picasa random image: ".$this->URL."<img src=\"$randomPic\" alt=\"\">", "created");
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
	protected function showAlbumContent($userId, $albumName) {
		$url = 'http://picasaweb.google.com/data/feed/api/user/' . urlencode($userId) . '/album/' . urlencode($albumName);
		$xml = $this->curlit($url);
		$xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);

		$dom = new domdocument;
		$dom->loadXml($xml);

		$xpath = new domxpath($dom);
		$nodes = $xpath->query('//entry');
		foreach ($nodes as $node) {
			$tmp[]['src'] = $xpath->query('.//media:thumbnail/@url', $node)->item(0)->textContent;
		}
		return $tmp;
	}

	//THIS IS BASED ON http://blogoscoped.com/archive/2007-03-22-n84.html
	protected function getAlbums($userId) {
		$url = 'http://picasaweb.google.com/data/feed/api/user/' . urlencode($userId) . '?kind=album';
		$xml = $this->curlit($url);
		$xml = str_replace("xmlns='http://www.w3.org/2005/Atom'", '', $xml);

		$dom = new domdocument;
		$dom->loadXml($xml);

		$xpath = new domxpath($dom);
		$nodes = $xpath->query('//entry');
		foreach ($nodes as $node) {
			$tmp[] = $xpath->query('title', $node)->item(0)->textContent;
		}
		return $tmp;
	}


}
