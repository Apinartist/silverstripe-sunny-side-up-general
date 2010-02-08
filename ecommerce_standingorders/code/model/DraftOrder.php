<?php

/**
 * Used to review an order after it is posted by a StandingOrder
 * @author Michael Mitchell <michael@michaelmitchell.co.nz>
 */

class DraftOrder extends Order {
	
	public static $has_one = array(
		'StandingOrder' => 'StandingOrder',
	);
	
	/**
	 * Always return "Draft Order" as the status
	 */
	public function Status() {
		return 'Draft Order';
	}
	
	/**
	 * Publish the order
	 * @return null
	 */
	public function publishOrder() {
		$this->ClassName = 'Order';
		
		$modifiers = ShoppingCart::get_modifiers();
		
		if($modifiers) {
			 $this->createModifiers($modifiers, true);
		}
		
		$this->write();
	}
	
}