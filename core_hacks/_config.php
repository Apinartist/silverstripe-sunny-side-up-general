<?php


/**
*@author Nicolaas [at] sunnysideup.co.nz
*
**/

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START core_hacks MODULE ----------------===================
CoreHackDataObject::add_change_array(
	$fileAndFilder = "mysite/_config.php",
	$fromString = '//===================---------------- START core_hacks MODULE ----------------===================',
	$toString = '//===================---------------- START core_hacks MODULE [is working] ----------------==================='
);
//===================---------------- END core_hacks MODULE ----------------===================

