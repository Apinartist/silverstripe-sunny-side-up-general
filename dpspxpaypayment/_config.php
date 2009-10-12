<?php


/**
*@author Nicolaas [at] sunnysideup.co.nz
*
**/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START dpspxpaypayment MODULE ----------------===================
// *** set basics
//DpsPxPayComs::set_alternative_thirdparty_folder("dpspxpaypayment/code/thirdparty");
// *** set codes provide by DPS
/*
if(Director::isLive()){
	DpsPxPayComs::set_pxpay_userid("Code");
	DpsPxPayComs::set_pxpay_encryption_key("A");
}
else{
	DpsPxPayComs::set_pxpay_userid("Code_Dev");
	DpsPxPayComs::set_pxpay_encryption_key("B");
}
*/
// *** set credit cards to be excluded
//DpsPxPayPayment::remove_credit_card("Visa");
//DpsPxPayPayment::remove_credit_card("MasterCard");
//DpsPxPayPayment::remove_credit_card("American Express");
//DpsPxPayPayment::remove_credit_card("Dinners Club");
//DpsPxPayPayment::remove_credit_card("JCB");
//*** must have!
Director::addRules(50, array(
	DpsPxPayPayment_Handler::$url_segment . '/$Action/$ID' => 'DpsPxPayPayment_Handler',
));


//===================---------------- END dpspxpaypayment MODULE ----------------===================

