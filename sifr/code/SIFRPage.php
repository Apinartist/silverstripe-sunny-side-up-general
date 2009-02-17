<?php

class SIFRPage extends Page {
	static $db = array(
	);
	static $has_one = array(
   );


}

class TesterPage_Controller extends Page_Controller {
	function init() {
    $obj = new Sifr();
    $obj->loadSifr();
		parent::init();
	}

  function testme() {
  }

}

?>