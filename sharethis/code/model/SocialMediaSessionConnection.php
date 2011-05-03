<?php

/**
 * This class saves data for connections to social media
 *
 *
 **/

abstract class SocialMediaConnections extends DataObject {

	static $db = array(
		'Session' => 'Text'
	);
}
