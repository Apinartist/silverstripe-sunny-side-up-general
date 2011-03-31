<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@PicasaRandomImage::set_google_username("tester")
 *@PicasaRandomImage::set_number_of_folders(3)
 *@PicasaRandomImage::set_number_of_images_per_folder(2)
 *
 **/

class PicasaRandomImage extends DataObject {

	static $db = array(
		"URL" => "Varchar(255)",
		"Title" => "Varchar(255)",
		"ThumbnailURL" => "Varchar(255)",
		"ThumbnailWidth" => "Int",
		"ThumbnailHeight" => "Int",
		"FullURL" => "Varchar(255)",
		"FullWidth" => "Int",
		"FullHeight" => "Int",
		"Album" => "Varchar(255)"
	);

	protected static $google_username = '';
		static  function set_google_username($v) {self::$google_username = $v;}
		static  function get_google_username() {return self::$google_username;}

	protected static $number_of_folders = 3;
		static  function set_number_of_folders($v) {self::$number_of_folders = $v;}
		static  function get_number_of_folders() {return self::$number_of_folders;}

	protected static $number_of_images_per_folder = 3;
		static  function set_number_of_images_per_folder($v) {self::$number_of_images_per_folder = $v;}
		static  function get_number_of_images_per_folder() {return self::$number_of_images_per_folder;}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::$google_username) {
			// choose 5 random albums from the list
			$currentObjects = DataObject::get($this->ClassName);
			if($currentObjects) {
				$actuallyHas = $currentObjects->count();
				$shouldHave = self::$number_of_images_per_folder * self::$number_of_folders;
				if($actuallyHas == $shouldHave) {
					//do nothing
					return;
				}
				else {
					//reset
					DB::query("DELETE FROM ".$this->ClassName.";");
					DB::alteration_message("reset picasa random images", "created");
				}
			}
			// the feed URLs from where album and photo information will be fetched
			$albums_feed = 'http://picasaweb.google.com/data/feed/api/user/' . self::$google_username . '?kind=album';
			$album_feed = 'http://picasaweb.google.com/data/feed/api/user/' . self::$google_username . '?v=2';
			$photo_feed = 'http://picasaweb.google.com/data/feed/api/user/' . self::$google_username . '/albumid/';
			// read album feed into a SimpleXML object
			$albums = simplexml_load_file($albums_feed);
			if($albums) {
				$albumCount = $albums->children('http://a9.com/-/spec/opensearchrss/1.0/')->totalResults;
				//$albumcount = $photocount = (int) $album->children('http://schemas.google.com/photos/2007')->numphotos;
				if($albumCount) {
					$album_select = array_rand(range(1, $albumCount), self::$number_of_folders);
					$i = 0;
					foreach ($albums->entry as $album) {
						$i++;
						// if album is one of the chosen ones, continue
						if (in_array($i, $album_select)) {
							// get the number of photos for this album
							$photocount = (int) $album->children('http://schemas.google.com/photos/2007')->numphotos;
							if($photocount) {
								// choose 2 random photos from this album
								$photo_select = array_rand(range(1, $photocount), 2);
								// get the ID of the current album
								$album_id = $album->children('http://schemas.google.com/photos/2007')->id;
								// read photo feed for this album into a SimpleXML object
								$photos = simplexml_load_file($photo_feed . $album_id . '?v=2');
								$j = 0;
								foreach ($photos->entry as $photo) {
									if (in_array($j, $photo_select)) {
										$temp = array();
										// get the photo and thumbnail information
										$media = $photo->children('http://search.yahoo.com/mrss/');
										// full image information
										$group_content = $media->group->content;
										$obj = new PicasaRandomImage();
										$obj->FullURL = strval($group_content->attributes()->{'url'});
										$obj->FullWidth = intval($group_content->attributes()->{'width'});
										$obj->FullHeight = intval($group_content->attributes()->{'height'});
										// thumbnail information, get the 3rd (=biggest) thumbnail version
										// change the [2] to [0] or [1] to get smaller thumbnails
										$group_thumbnail = $media->group->thumbnail[2];
										$obj->ThumbnailURL = strval($group_thumbnail->attributes()->{'url'});
										$obj->ThumbnailWidth = intval($group_thumbnail->attributes()->{'width'});
										$obj->ThumbnailHeight = intval($group_thumbnail->attributes()->{'height'});
										$obj->Album = strval($album->title[0]);
										$obj->Title = strval($group_content->attributes()->{'title'});
										$obj->write();
										DB::alteration_message("adding picasa random image: ".$this->FullURL, "created");
									};
									$j++;
								}
							} else {user_error("no photos in album", E_USER_NOTICE); }
						}
					}
				} else {user_error("no album count", E_USER_NOTICE);}
			} else {user_error("no albums", E_USER_NOTICE);}
		} else {user_error("no google username", E_USER_NOTICE);}
	}


}
