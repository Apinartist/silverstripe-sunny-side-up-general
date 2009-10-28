<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@package ecommerce
 *@subpackage ecommercextras
 *@description: adds a dispatch log to order
 *
 **/

class OrderStatusLogInternal extends DataObject {


	public static $db = array(
		'Notes' => 'Text',
	);


	public static $has_one = array(
		'Author' => 'Member',
		'Order' => 'Order'
	);

	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	function onBeforeSave() {
		if(!$this->ID) {
			$this->AuthorID = Member::currentUser()->ID;
		}
		parent::onBeforeSave();
	}

}