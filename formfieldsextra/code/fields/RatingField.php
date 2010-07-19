<?php

/*
 *@author Romain[at]sunnysidep.co.nz
 *
 **/


class RatingField extends OptionsetField {

	function __construct($name, $title = "", $source = array(), $value = "", $form = null) {
		parent::__construct($name, $title, $source, $value, $form);
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript('survey/javascript/jquery/ui.core.js');
		Requirements::javascript('survey/javascript/jquery/ui.slider.js');
		Requirements::javascript('survey/javascript/ratingfield.js');
		Requirements::css('survey/css/ui.core.css');
		Requirements::css('survey/css/ui.slider.css');
		Requirements::css('survey/css/ui.theme.css');
	}

}

?>
