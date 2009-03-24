<?php

class GoogleAnalytics extends DataObjectDecorator {

	static $googleAnalyticsCode;

	function GoogleAnalyticsCode() {
		if(Director::isLive()) {
			return self::$googleAnalyticsCode;
		}
		return array();
	}
}