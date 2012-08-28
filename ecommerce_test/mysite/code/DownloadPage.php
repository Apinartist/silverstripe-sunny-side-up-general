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
			"DownloadLink" => "assets/download-all/ecommerce.zip"
			),
		"sapphire" => array(
			"Title" => "Sapphire 2.4.7",
			"SVNLink" => "https://github.com/silverstripe/sapphire/tags/2.4.7",
			"GITLink" => "https://github.com/silverstripe/sapphire/tree/2.4.7",
			"DownloadLink" => ""
			),
		"cms" => array(
			"Title" => "CMS 2.4.7",
			"SVNLink" => "https://github.com/silverstripe/silverstripe-cms/tags/2.4.7",
			"GITLink" => "https://github.com/silverstripe/silverstripe-cms/tree/2.4.7",
			"DownloadLink" => ""
			),
		"payment" => array(
			"Title" => "Payment Module 0.3.0",
			"SVNLink" => "https://github.com/silverstripe-labs/silverstripe-payment/tags/0.3.0",
			"GITLink" => "https://github.com/silverstripe-labs/silverstripe-payment/tree/0.3.0",
			"DownloadLink" => ""
			),
		"mysite" => array(
			"Title" => "Mysite",
			"SVNLink" => "http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/mysite",
			"GITLink" => "",
			"DownloadLink" => "assets/downloads/mysite"
			),
		"themes" => array(
			"Title" => "Mysite",
			"SVNLink" => "http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/themes",
			"GITLink" => "",
			"DownloadLink" => "assets/downloads/themes"
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
			$svnLink = "https://silverstripe-ecommerce.googlecode.com/svn/modules/$folder/trunk/";
			if($folder == "ecommerce"){
				$svnLink = "https://silverstripe-ecommerce.googlecode.com/svn/trunk/";
			}
			$gitLink = "https://github.com/sunnysideup/silverstripe-$folder";
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
		foreach($this->defaultDownloadArray as $key => $folderArray) {
			if(!$this->canDownload() || !file_exists(Director::baseFolder()."/".$this->defaultDownloadArray[$key]["DownloadLink"])) {
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
					if(
						substr($file, 0, 10) == "ecommerce_" ||
						substr($file, 0, 8) == "payment_" ||
						$file == "ecommerce" ||
						$file == "themes" ||
						$file == "mysite"
					) {
						$array[] = $file;
					}
				}
				closedir($dh);
			}
		}
		return $array;
	}

}
