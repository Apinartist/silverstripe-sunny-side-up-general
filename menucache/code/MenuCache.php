<?php

class MenuCache extends DataObjectDecorator {
	function extraDBFields(){
		return array(
			'db' =>  array('MenuCache' => 'HTMLText' )
		);
	}

}

class MenuCache_controller extends Extension {


}
