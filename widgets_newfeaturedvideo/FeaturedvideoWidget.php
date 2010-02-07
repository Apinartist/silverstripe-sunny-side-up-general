<?php

/**
 * @author nicolaas [at] sunnysideup.co.nz
 * @based on: Featured Video Widget 0.1 for the SilverStripe Blog Module by abimanyuraja@gmail.com
 **/

class FeaturedvideoWidget extends Widget {

	static $db = array(
		"VideoProvider" => "Varchar",
		"Url" => "Text"
	);

	protected static $small_video_width = 200;
		static function set_small_video_width($v) {self::$small_video_width;}

	protected static $small_video_height = 150;
		static function set_small_video_height($v) {self::$small_video_height;}

	static $title = "Featured Video";

	static $cmsTitle = "Featured Video";

	static $description = "Display one video from Youtube, Google Video or Vimeo. Full sized videos can also be viewed";

	function GetFeaturedVideo() {
	 try {

		Requirements::javascript("widgets_featuredvideo/js/swfobject.js");
		Requirements::themedCSS("featuredvideo.css");

		$parsedUrl = parse_url($this->Url);

		switch ($this->VideoProvider) {
		case "youtube":
			parse_str($parsedUrl['query'], $id);
			$videoUrl =  "http://youtube.com/v/" . $id['v'];
			$width = 425;
			$height = 355;
			break;
		case "googleVideo":
			parse_str($parsedUrl['query'], $id);
			$videoUrl =  "http://video.google.com/googleplayer.swf?docid=" . $id['docid'];
			$width = 410;
			$height = 336;
			break;
		case "vimeo":
			$id = split("/", $parsedUrl['path']);
			$videoUrl =  "http://www.vimeo.com/moogaloop.swf?clip_id=" . $id[1] . "&amp;server=www.vimeo.com&amp;fullscreen=1";
			$width = 480;
			$height = 360;
			break;
		default:
			$videoUrl = "";
		}

		$output = new DataObjectSet();

		$output->push(new ArrayData(array(
					"Url" =>  $videoUrl,
					"Width" => $width,
					"Height" => $height
		)));
		Requirements::customScript("
			featuredvideo.setSmallWidth( ".self::$small_video_width." );
			featuredvideo.setSmallHeight( ".self::$small_video_height." );
			featuredvideo.setLargeWidth( $width );
			featuredvideo.setLargeHeight( $height );
			featuredvideo.setUrl( '$videoUrl' );
		");
		return $output;

		} catch(Exception $e) {
			return false;
		}

	}

	function getCMSFields() {

		return new FieldSet(
			new TextField("Url", "Video URL"),
			new OptionsetField(
					$name = "VideoProvider",
					$title = "Video Site",
					$source = array(
						"youtube" => "Youtube",
						"googleVideo" => "Google Video",
						"vimeo" => "Vimeo"
						),
					$value = "youtube"
				)
		);
	}
}

