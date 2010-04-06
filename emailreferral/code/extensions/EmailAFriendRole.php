<?php

class EmailAFriendExtension extends DataObjectDecorator {

	static $sender_email_address;
	static $sender_name;
	static $default_message;
	static $max_message_phour_pip;
	static $mail_subject;

	public static function set_sender_email_address($senderEmailAddress) {self::$sender_email_address = $senderEmailAddress;}
	public static function set_sender_name($senderName) {self::$sender_name = $senderName;}
	public static function set_default_message($defaultMessage) {self::$default_message = $defaultMessage;}
	public static function set_max_message_phour_pip($maxMessagePhourPip) {self::$max_message_phour_pip = $maxMessagePhourPip;}
	public static function set_mail_subject($mailSubject) {self::$mail_subject = $mailSubject;}

	public static function get_ip_user() {
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else return isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
	}

	function EmailAFriendLink() {
		return $this->owner->Link() . 'emailafriend';
	}

	function EmailAFriendForm() {
		return new EmailAFriendForm($this->owner, 'EmailAFriendForm', $this->owner->ID);
	}
}


class EmailAFriendExtension_Controller extends Extension {
	static $allowed_actions = array('EmailAFriendForm');
}