<?php

class ImageGalleryHolder extends Page {

	static $icon = 'cms/images/treeicons/folder';

	static $allowed_children = array('ImageGalleryPage');

	static $default_child = 'ImageGalleryPage';
}

class ImageGalleryHolder_Controller extends Page_Controller {

	function MyChildGalleries(){
		return DataObject::get("ImageGalleryPage", "\"ParentID\" = ".$this->ID." AND \"ShowInSearch\" = 1");
	}


}
