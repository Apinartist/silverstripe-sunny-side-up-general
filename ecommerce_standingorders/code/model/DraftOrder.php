<?php

/**
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
	
	public function sendReceipt() {
		$this->sendEmail('DraftOrder_ReceiptEmail');
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

class DraftOrder_ReceiptEmail extends Email {

	protected $ss_template = 'DraftOrder_ReceiptEmail';

}