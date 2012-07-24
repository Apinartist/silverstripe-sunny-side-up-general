<?php


class CleanEcommerceTables extends Controller {

	function init() {
		parent::init();
		if(!Permission::check("ADMIN") && ! Director::isDev()) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
	}

	public function reset(){
		$sql = mysql_query("SHOW TABLES") or die(mysql_error());
		while($row = mysql_fetch_array($sql)){
			DB::query("TRUNCATE TABLE \"".$row[0]."\";");
			DB::alteration_message("deleting ".$row[0], "deleted");
		}
		echo "<hr /><hr /><hr /><hr /><hr /><a href=\"/dev/build\">build database</a>
		<script type=\"text/javascript\">window.location = \"/dev/build/?flush=1\";</script>"
		;
	}



}
