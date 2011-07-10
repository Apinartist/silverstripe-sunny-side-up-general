<?php
/**
 * ProductPage.php: Sub-class of Page
 * Contains info about a product
 * @created 14/10/2008
 */

class CertificationPage extends Page {

	/**
	 *Standard SS Static
	 **/ 
	static $icon = "business_directory/images/treeicons/CertificationPage";

	/**
	 *Standard SS Static
	 **/ 
	static $db = array(
		"Website" => "Varchar(255)",
	);

	/**
	 *Standard SS Static
	 **/ 
	static $has_one = array(
		"Logo" => "Image",
	);

	/**
	 *Standard SS Static
	 **/ 	
	static $belongs_many_many = array(
		'Businesses' => 'BusinessPage'
	);

	/**
	 *Standard SS Static
	 **/ 	
	static $can_be_root = false;

	/**
	 *Standard SS Method
	 **/ 	
	function getCMSFields( $cms ) {
		$fields = parent::getCMSFields( $cms );
		$fields->addFieldToTab("Root.Content.MoreInfo", new TextField("Website") );
		$fields->addFieldToTab("Root.Content.Logo", new ImageField("Logo", "Logo", $value = null, $form = null, $rightTitle = null, $folderName = "/assets/CertificationLogos/") );
		return $fields;
	}

	/**
	 * returns the number of businesses that carry this certification.
	 *@return Integer
	 **/ 	
	function NumberOfBusinesses(){
		return $this->Businesses()->count();
	}

}

class CertificationPage_Controller extends Page_Controller {

}
