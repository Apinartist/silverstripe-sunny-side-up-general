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
					<div class="Actions"><input id="ModifyStandingOrderUpdate"  class="action" type="button" value="Save changes to your Standing Order #$orderID" onclick="window.location='{$updateLink}';" /></div>
HTML
					)
				);

			}
			else {
				$fields->push(new LiteralField('createStandingOrder',
<<<HTML
						<div class="Actions"><input id="ModifyStandingOrderCreate" class="action" type="button" value="Create a new Standing Order" onclick="window.location='{$createLink}';" /></div>
HTML
					)
				);
			}
			Requirements::customScript("jQuery(document).ready(function(){jQuery(\"input[name='action_processOrder']\").hide();});", "hide_action_processOrder");
		}
		else if(Member::currentMember()) {
			if(!Session::get("DraftOrderID")) {
				$fields->push(new LiteralField('createStandingOrder',
<<<HTML
					<div class="Actions"><input  id="ModifyStandingOrderCreate" class="action" type="button" value="Turn this Order into a Standing Order" onclick="window.location='{$createLink}';" /></div>
HTML
					)
				);
				$page = DataObject::get_one("StandingOrdersPage");
				if($page) {
					$fields->push(new LiteralField("whatAreStandingOrders",
<<<HTML
					<div id="WhatAreStandingOrders">$page->WhatAreStandingOrders</div>
HTML
					));
				}
			}
			else {
					$fields->push(new LiteralField("whatAreStandingOrders",
<<<HTML
					<div id="WhatAreStandingOrders">This order is based on a Standing Order.</div>
HTML
					));
			}
		}
		else {
			$page = DataObject::get_one("StandingOrdersPage");
			if($page) {
				$fields->push(new LiteralField("whatAreStandingOrders",
<<<HTML
					<div id="WhatAreStandingOrders">$page->OnceLoggedInYouCanCreateStandingOrder</div>
HTML
				));
			}
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