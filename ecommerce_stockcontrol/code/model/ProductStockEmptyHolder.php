<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description:
 * this DataObject is
 *
 **/

class ProductStockEmptyHolder extends DataObject {

	static $db = array(
	);

	static $has_one = array(
	);

	static $defaults = array(
	);

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
	);

	public static $field_labels = array(
	);

	public static $summary_fields = array(
	);

	public static $default_sort = "";

	public static $singular_name = "Product Stock Manager";

	public static $plural_name = "Product Stock Managers";

	public function canCreate() {return false;}

	public function canEdit() {return false;}

	public function canDelete() {return false;}

	public function canView() {return $this->canDoAnything();}

	protected function canDoAnything() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		return true;
	}


}
