<?php

class OrderAdmin extends ModelAdmin {

	static $url_segment = 'orders';
	static $menu_title = 'Orders';

	public static $managed_models = array('Order', 'Payment');

	public static $collection_controller_class = 'OrderAdmin_CollectionController';
	public static $record_controller_class = 'OrderAdmin_RecordController';
}

class OrderAdmin_CollectionController extends ModelAdmin_CollectionController {

	public function CreateForm() {return false;}
}

class OrderAdmin_RecordController extends ModelAdmin_RecordController {

	public function EditForm() {
		$form = parent::EditForm();
		$form->Actions()->removeByName('Delete');
		return $form;
	}
}
