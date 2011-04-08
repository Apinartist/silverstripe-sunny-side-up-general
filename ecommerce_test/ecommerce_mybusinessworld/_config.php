<?php

/**
 *@description: copy the commented lines to your own mysite/_config.php file for editing...
 *
 *
 **/


Director::addRules(50, array(
	ReceiveFromMyBusinessWorld::get_url_segment() . '/$Action/$ID/$OtherID' => 'ReceiveFromMyBusinessWorld'
));

// copy the lines below to your mysite/_config.php file and set as required.
// __________________________________START ECOMMERCE MY BUSINESS WORLD MODULE CONFIG __________________________________
// __________________________________ END ECOMMERCE MY BUSINESS WORLD MODULE CONFIG __________________________________



