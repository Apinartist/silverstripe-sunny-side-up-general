<?php

 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 *
 *
 *
 *
 */

class WebPortfolioItem extends DataObject {

	static $db = array(
		"WebAddress" => "Varchar(255)",
		"NoLongerActive" => "Boolean",
		"NotPubliclyAvailable" => "Boolean",
		"Favourites" => "Boolean",
		"Notes" => "Varchar(255)",
		"Client" => "Varchar(255)"
	);

	static $has_one = array(
		"Agent" => "WebPortfolioAgent",
		"Image" => "Image"
	);

	static $many_many (
		"WhatWeDid" => "WebPortfolioWhatWeDidDescriptor"
	);

	static $defaults = array(
		"WebAddress" => "http",
		"NoLongerActive" => false,
		"Favourites" => false
	);

	public static $default_sort = "WebAddress";

	public static $searchable_fields = array(
		"WebAddress",
		"Client",
		"NoLongerActive",
		"NotPubliclyAvailable",
		"Favourites"
		"Agent.Name"
	);

	public static $summary_fields = array(
		"WebAddress"
		"Client"
	);

	public static $singular_name = "Item";

	public static $plural_name = "Items";

}