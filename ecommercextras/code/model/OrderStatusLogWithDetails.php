<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@package ecommerce
 *@subpackage ecommercextras
 *@description: adds a dispatch log to order
 *
 **/

class OrderStatusLogWithDetails extends OrderStatusLog {


	public static $db = array(
		'DispatchedBy' => 'Varchar(255)',
		'DispatchedOn' => 'Date',
		'DispatchTicket' => 'Varchar(255)',
		'PaymentCode' => 'Varchar(255)',
		'PaymentOK' => 'Boolean'
	);


	public static $has_many = array();

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array();

	public function canDelete($member = null) {
		return false;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->ID) {
			$this->AuthorID = Member::currentUser()->ID;
		}
		if(!$this->OrderID) {
			user_error("Trying to save OrderStatusLogWithDetails without Order ID", E_USER_WARNING);
		}
	}

}