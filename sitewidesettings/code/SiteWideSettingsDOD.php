<?php

/**
 *
 *
 * @author nicolaas [at] sunnysideup.co.nz
 * @package cms
 * @subpackage sitewidesettings
 */
class SiteWideSettingsDOD extends DataObjectDecorator {

	protected static $extra_statics = null;

	static function set_extra_statics(array $array) {
		self::$extra_statics = $array;
	}

	function extraStatics() {
		return self::$extra_statics;
	}


}
