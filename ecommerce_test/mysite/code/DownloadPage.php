<?php


class DownloadPage extends Page {

	function canDownload(){
		if($member = Member::currentMember()) {
			if($member->IsShopAdmin()) {
				return true;
			}
		}
	}

}

class DownloadPage_Controller extends Page_Controller {

	protected $defaultDownloadArray = array(
		"all" => array(
			"Title" => "Entire Site",
			"SVNLink" => "http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/",
			"GITLink" => "",
			"DownloadLink" => ""
		),
		"ecommerce" => array(
			"Title" => "Main Ecommerce Folder",
			"SVNLink" => "https://silverstripe-ecommerce.googlecode.com/svn/trunk",
			"GITLink" => "https://github.com/sunnysideup/silverstripe-ecommerce",
			"DownloadLink" => "assets/download-all/ecommerce.zip"
		)
	);

	function init() {
		parent::init();
	}

	function index() {
		if($this->canDownload()){
			$this->createDownloads();
		}
		return array();
	}

	function Downloads(){
		$dos = new DataObjectSet();
		$folders = $this->getFolderList();
		foreach($folders as $folder) {
			$svnLink = "https://silverstripe-ecommerce.googlecode.com/svn/modules/$folder/trunk";
			if(substr($folder, 0, 10) == "ecommerce_") {
				$gitLink = "https://github.com/sunnysideup/silverstripe-$folder";
			}
			$downloadLink = "assets/downloads/$folder.zip";
			if(!isset($this->defaultDownloadArray[$folder])) {
				$this->defaultDownloadArray[$folder] = array(
					"Title" => $folder,
					"SVNLink" => $svnLink,
					"GITLink" => $gitLink,
					"DownloadLink" => $downloadLink,
				);
			}
		}
		if(!$this->canDownload()) {
			foreach($this->defaultDownloadArray as $key => $folderArray) {
				unset($this->defaultDownloadArray[$key]["DownloadLink"]);
			}
		}
		foreach($this->defaultDownloadArray as $folderArray) {
			$dos->push(new ArrayData($folderArray));
		}
		return $dos;
	}

	private function createDownloads() {
		$folders = $this->getFolderList();
		foreach($folders as $folder) {
			exec('
				cd '.Director::baseFolder().'/
				zip -r assets/downloads/'.$folder.'.zip '.$folder.'/ -x "*.svn/*" -x "*.git/*"'
			);
		}
		exec('
			cd '.Director::baseFolder().'/assets/download-all/
			zip -r assets/download-all/ecommerce.zip '.Director::baseFolder().'/assets/downloads/ -x "*.svn/*" -x "*.git/*"'
		);
	}

	private function getFolderList() {
		$dir = Director::baseFolder();
		$array = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					is_dir($file);
					if(substr($file, 0, 10) == "ecommerce_" || substr($file, 0, 8) == "payment_" ) {
						$array[] = $file;
					}
				}
				closedir($dh);
			}
		}
		return $array;
	}

}
