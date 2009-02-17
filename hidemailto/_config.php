<?php

/**
 * CONFIG:
 * Specify the domains you're allowed to send to. This can be either the
 * string '*' (for creating a link for any domain), or an array of domains to
 * limit to certain domains only.
 */
HideEmail_Controller::set_allowed_domains('*');

/**
 * CONFIG:
 * You can comment out/remove this line if you don't want to use $HideEmailLink
 * on Member objects in the system (it does add some extra processing time to
 * viewing Member objects if you have these)
 */
DataObject::add_extension('Member', 'HideEmail_Role');

/**
 * CONFIG:
 * This allows you to specify what happens to links that go to mailto/x/y/z/
 * point to. If you change this, take care to also change the HideEmailLink
 * function on the HideEmail_Role object to use the same short URL.
 */
Director::addRules(50, array(
	'mailto/$Action/$ID/$OtherID' => 'HideEmail_Controller'
));
?>