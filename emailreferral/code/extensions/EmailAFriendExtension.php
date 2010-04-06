<?php

class EmailAFriendExtension extends DataObjectDecorator {

	protected static $sender_email_address;
		public static function set_sender_email_address($senderEmailAddress) {self::$sender_email_address = $senderEmailAddress;}
		public static function get_sender_email_address() {return self::$sender_email_address;}

	protected static $sender_name;
		public static function set_sender_name($senderName) {self::$sender_name = $senderName;}
		public static function get_sender_name() {return self::$sender_name;}

	protected static $default_message;
		public static function set_default_message($defaultMessage) {self::$default_message = $defaultMessage;}
		public static function get_default_message() {return self::$default_message;}

	protected static $max_message_phour_pip;
		public static function set_max_message_phour_pip($maxMessagePhourPip) {self::$max_message_phour_pip = $maxMessagePhourPip;}
		public static function get_max_message_phour_pip() {return self::$max_message_phour_pip;}

	protected static $mail_subject;
		public static function set_mail_subject($mailSubject) {self::$mail_subject = $mailSubject;}
		public static function get_mail_subject() {return self::$mail_subject;}

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