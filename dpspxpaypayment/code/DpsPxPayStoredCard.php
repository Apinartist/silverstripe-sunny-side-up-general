<?php

class DpsPxPayStoredCard extends DataObject {

	public static $db = array(
		'CardName' => 'Varchar', //last 4 didgets of the card
		'CardHolder' => 'Varchar', //last 4 didgets of the card
		'CardNumber' => 'Varchar', //last 4 didgets of the card
		'BillingID' => 'Varchar',
	);
	
	public static $has_one = array(
		'Member' => 'Member'
	);
	
}