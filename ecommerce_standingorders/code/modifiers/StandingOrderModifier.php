<?php

class StandingOrderModifier extends OrderModifier {
	
	protected static $is_chargable = false;
	
	public static function show_form() {
		return true;
	}
	
	public static function get_form($controller) {
		$fields = new FieldSet();
		
		$orderID = Session::get('StandingOrder');
		
		$order = DataObject::get_by_id('StandingOrder', $orderID);
		
		$createLink = StandingOrdersPage::get_standing_order_link('create');
		$updateLink = StandingOrdersPage::get_standing_order_link('update', $orderID);
		$cancelLink = StandingOrdersPage::get_standing_order_link('cancel', $orderID);
		
		if($orderID && Member::currentMember()) {
			if($order->CanModify()) $fields->push(new LiteralField('modifyStandingOrder', 
<<<HTML
					<p>Currently making changes to standing order #$orderID</p>
HTML
				)
			);
			$fields->push(new LiteralField('modifyStandingOrder', 
<<<HTML
					<input class="action" type="button" value="Create a new standing order" onclick="window.location='{$createLink}';" />
HTML
				)
			);
			
			if($order->CanModify()) $fields->push(new LiteralField('modifyStandingOrder', 
<<<HTML
					<input class="action" type="button" value="Save changes to your standing order" onclick="window.location='{$updateLink}';" />
HTML
				)
			);
		}
		else if(Member::currentMember()) {
			$fields->push(new LiteralField('createStandingOrder', 
<<<HTML
					<input class="action" type="button" value="Create a new standing order" onclick="window.location='{$createLink}';" />
HTML
				)
			);
		}
		
		return new OrderModifierForm($controller, 'ModifierForm', $fields, new FieldSet());
	}
	
	public function LiveAmount() {
		return null;
	}
	
	public function CanRemove() {
		return false;
	}

	public function ShowInTable() {
		return false;
	}
	
}