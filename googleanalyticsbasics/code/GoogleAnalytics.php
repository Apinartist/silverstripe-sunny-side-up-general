<?php

class GoogleAnalytics extends SiteTreeDecorator {

	static $googleAnalyticsCode;

	function GoogleAnalyticsCode() {
		if(Director::isLive()) {
			return self::$googleAnalyticsCode;
		}
		return array();
	}
}