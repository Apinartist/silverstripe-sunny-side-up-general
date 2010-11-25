<?php

/**
  *@author nicolaas [at] sunnysideup.co.nz
  **/

class FlowPlayer extends File {

	protected static $flow_player_config_file = "flowplayer/javascript/FlowPlayerConfig.js";
		static function set_flow_player_config_file($v) {self::$flow_player_config_file = $v;}
		static function get_flow_player_config_file() {return self::$flow_player_config_file;}

	function AbsoluteLink($IDString = "FlowPlayer"){
		Requirements::javascript("flowplayer/thirdparty/flowplayer-3.2.4.min.js");
		Requirements::javascript(self::get_flow_player_config_file());
		Requirements::customScript('$f("'.$IDString.'", "flowplayer/thirdparty/flowplayer-3.2.5.swf", FlowPlayerConfig);', "FlowPlayerSWF");
		Requirements::themedCSS("FlowPlayer");
		return $this->getAbsoluteURL();
	}



}
