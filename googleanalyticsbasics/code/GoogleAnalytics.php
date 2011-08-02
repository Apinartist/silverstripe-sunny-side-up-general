<?php

class GoogleAnalytics extends SiteTreeDecorator {

	public static $googleAnalyticsCode;

	function GoogleAnalyticsCode() {
		if(Director::isLive()) {
			return self::$googleAnalyticsCode;
		}
		return null;
	}

}
