<?php

class FriendEmail extends DataObject {

	static $db = array(
		'To' => 'Text',
		'Message' => 'Text',
		'From' => 'Text',
		'IPAddress' => 'Text'
	);

	static $has_one = array(
		'Page' => 'Page'
	);

	static $searchable_fields = array('To', 'Message', 'From', 'IPAddress', 'Page.Title');

	static $summary_fields = array('Created', 'To', 'Message', 'From', 'IPAddress', 'Page.Title');

	static $singular_name = 'Friend Email';

	static $plural_name = 'Friend Emails';

	static $default_sort = 'Created DESC';
}

