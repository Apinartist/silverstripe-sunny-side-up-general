<?php

/** how it works:

 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz

	make sure that live and local site have the same codebase...
	run /dev/build/ on your live site
	go to /dump/import/ on your live site to download an sql text file
	run dev/build/ on your local site
	go to your local phpMyAdmin and import the file


*/

Director::addRules(8, array(
	//'Security/$Action/$ID' => 'Security',
	'dump//$Action' => 'MySQLDump'
));
