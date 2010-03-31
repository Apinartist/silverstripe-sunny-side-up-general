<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class EcommerceVoteDataDecorator extends DataObjectDecorator {

	function HasEcommerceVote() {
		return DataObject::get_one("EcommerceVote", "SessionID = '".Session_ID()."' AND PageID = ".$this->owner->ID);
	}


}
