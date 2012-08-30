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
			"Title" => "Entire Site (use svn link for best results)",
			"SVNLink" => "http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/",
			"GITLink" => "",
			"DownloadLink" => "assets/download-all/ecommerce.zip"
			),
		"sapphire" => array(
			"Title" => "Sapphire 2.4.7 (provided by Silverstripe Ltd)",
			"FolderPadded" => "sapphire",
			"SVNLink" => "https://github.com/silverstripe/sapphire/tags/2.4.7",
			"GITLink" => "https://github.com/silverstripe/sapphire/tree/2.4.7",
			"DownloadLink" => ""
			),
		"cms" => array(
			"Title" => "CMS 2.4.7 (provided by Silverstripe Ltd)",
			"FolderPadded" => "cms",
			"SVNLink" => "https://github.com/silverstripe/silverstripe-cms/tags/2.4.7",
			"GITLink" => "https://github.com/silverstripe/silverstripe-cms/tree/2.4.7",
			"DownloadLink" => ""
			),
		"payment" => array(
			"Title" => "Payment Module 0.3.0 (provided by Silverstripe Ltd)",
			"FolderPadded" => "payment",
			"SVNLink" => "https://github.com/silverstripe-labs/silverstripe-payment/tags/0.3.0",
			"GITLink" => "https://github.com/silverstripe-labs/silverstripe-payment/tree/0.3.0",
			"DownloadLink" => ""
			),
		"mysite" => array(
			"Title" => "mysite (example only)",
			"SVNLink" => "http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/mysite",
			"GITLink" => "",
			"DownloadLink" => "assets/downloads/mysite"
			),
		"themes" => array(
			"Title" => "themes (example only)",
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
					"FolderPadded" => $folder,
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
		ksort($this->defaultDownloadArray);
		foreach($this->defaultDownloadArray as $folder => $folderArray) {
			$folderArray["Folder"] = $folder;
			if(isset($folderArray["FolderPadded"])) {
				$folderArray["FolderPadded"] = str_pad(trim($folderArray["FolderPadded"]), 45, " ",STR_PAD_RIGHT);
			}
			if(isset($folderArray["GITLink"])) {
				$folderArray["GITLinkGIT"] = str_pad(str_replace("https://", "git://", $folderArray["GITLink"]), 85, " ",STR_PAD_RIGHT);
			}

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
