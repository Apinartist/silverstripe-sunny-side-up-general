<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description allows you to only show some newsletters for "add me to" in the subscribe form
 **/


class NewsletterSubscribePage extends SubscribeForm {

	static $hide_ancestor = "SubscribeForm";

	static $has_many = array(
		"AvailableNewsletterTypes" => "NewsletterTypes"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$newsletterTypes = DataObject::get("NewsletterType");
		if($newsletterTypes) {
			$field = new CheckboxSetField("AvailableNewsletterTypes", "<h4>Select newsletters available for subscription</h4>", $newsletterTypes,$newsletterTypes);
		}
		else {
			$field = new LiteralField("NoNewsletters", "<p>You haven't define any newsletter yet, please go to <a href=\"admin/newsletter\">newsletter</a> to define some newsletter types</p>");
		}
		$fields ->addFieldToTab("Root.Content.NewslettersAvailable", $field);
		return $fields;
	}


	public function Newsletters() {
		return $this->AvailableNewsletterTypes();
	}

}
