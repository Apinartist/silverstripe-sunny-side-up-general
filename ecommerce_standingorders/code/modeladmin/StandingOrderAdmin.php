<?php

class StandingOrderAdmin extends ModelAdmin {

	public static $managed_models = array(
		'StandingOrder'
	);

	public static $url_segment = 'standing-orders';

	public static $menu_title = 'Standing Orders';

	public static $collection_controller_class = 'StandingOrderAdmin_CollectionController';


}

class StandingOrderAdmin_CollectionController extends ModelAdmin_CollectionController{
	//manages everything to do with overall control (e.g. search form, import, etc...)
	public function SearchForm() {
		$form = parent::SearchForm();
		$fields = $form->Fields();
		$source = ArrayLib::valuekey(StandingOrder::get_delivery_days()); //note, valuekey is a SS function
		$fields->replaceField("DeliveryDay", new DropdownField("DeliveryDay", "Delivery Day", $source, null, null, "(Any)"));
		$source = StandingOrder::get_period_fields();
		$fields->replaceField("Period", new DropdownField("Period", "Period", $source, null, null, "(Any)"));
		return $form;
	}
}
