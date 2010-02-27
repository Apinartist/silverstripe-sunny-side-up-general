<?php

class StandingOrderModifier extends OrderModifier {

	protected static $is_chargable = false;

	public static function show_form() {
		return true;
	}

	public static function get_form($controller) {
		$fields = new FieldSet();

		$orderID = Session::get('StandingOrder');

		$createLink = StandingOrdersPage::get_standing_order_link('create');

		if($orderID && Member::currentMember()) {
			$order = DataObject::get_by_id('StandingOrder', $orderID);

			$updateLink = StandingOrdersPage::get_standing_order_link('update', $orderID);
			$cancelLink = StandingOrdersPage::get_standing_order_link('cancel', $orderID);

			if($order->CanModify()) {
				 $fields->push(new LiteralField('modifyStandingOrder',
<<<HTML
					<p id="ModifyStandingOrderNote">Currently making changes to standing order #$orderID</p>
HTML
					)
				);
			}

			$fields->push(new LiteralField('createStandingOrder',
<<<HTML
					<input id="ModifyStandingOrderCreate" class="action" type="button" value="Create a new Standing Order" onclick="window.location='{$createLink}';" />
HTML
				)
			);

			if($order->CanModify()) {
				 $fields->push(new LiteralField('modifyStandingOrder',
<<<HTML
					<input id="ModifyStandingOrderUpdate"  class="action" type="button" value="Save changes to your Standing Order" onclick="window.location='{$updateLink}';" />
HTML
					)
				);
			}
		}
		else if(Member::currentMember()) {
			$fields->push(new LiteralField('createStandingOrder',
<<<HTML
					<input  id="ModifyStandingOrderCreate" class="action" type="button" value="Create a new Standing Order" onclick="window.location='{$createLink}';" />
HTML
				)
			);
		}
		$page = DataObject::get_one("StandingOrdersPage");
		if($page) {
			$fields->push(new LiteralField("whatAreStandingOrders",
<<<HTML
				<div id="WhatAreStandingOrders">$page->WhatAreStandingOrders</div>
HTML
			));
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