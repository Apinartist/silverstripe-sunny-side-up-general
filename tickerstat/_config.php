<?php



/*
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 **/

Director::addRules(50, array(
	'statistics//$Action/$ID/$Batch' => 'TickerStat_Controller'
));

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START tickerstat MODULE ----------------===================
//===================---------------- END tickerstat MODULE ----------------===================
