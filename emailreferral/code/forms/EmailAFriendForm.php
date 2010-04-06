<?php

class EmailAFriendForm extends Form {

	protected static $friend_email_address_label = "Friend#039;s Email Address";

	protected static $message_label = "Message";

	protected static $your_email_address_label = "Your email address";

	protected static $send_label = 'Send';

	static function set_friend_email_address_label($v) {self::$friend_email_address_label = $v;}
	static function set_message_label($v) {self::$message_label = $v;}
	static function set_your_email_address_label($v) {self::$your_email_address_label = $v;}
	static function set_send_label($v) {self::$send_label = $v;}

	function __construct($controller, $name, $id) {
		$fields[] = new TextField('To', self::$friend_email_address_label);
		$fields[] = new TextareaField('Message', self::$message_label, 5, 20, EmailAFriendExtension::$default_message ? EmailAFriendExtension::$default_message : '');
		$fields[] = new EmailField('YourMailAddress', self::$your_email_address_label);
		$fields[] = new HiddenField('PageID', 'PageID', $id);

		$fields = new FieldSet($fields);

		$actions = new FieldSet(new FormAction('sendEmailAFriend', self::$send_label));

		$requiredFields = new RequiredFields(array('To', 'Message'));

		parent::__construct($controller, $name, $fields, $actions, $requiredFields);
	}

	function sendEmailAFriend($RAW_data, $form) {
		$data = Convert::raw2sql($RAW_data);
		if($page = DataObject::get_by_id('Page', $data['PageID'])) $pageLink = $page->AbsoluteLink();

		$tos = explode(',', $data['To']);
		$toList = array();
		foreach($tos as $to) $toList = array_merge($toList, explode(';', $to));
		if($data['YourMailAddress']) $toList[] = $data['YourMailAddress'];
		$ip = EmailAFriendExtension::get_ip_user();
		$count = 0;
		if(EmailAFriendExtension::$max_message_phour_pip) {
			$anHourAgo = date('Y-m-d H:i:s', mktime(date('G') - 1, date('i'), date('s'), date('n'), date('j'), date('Y')));
			if($friendMails = DataObject::get('FriendEmail', "`IPAddress` = '$ip' AND `Created` > '$anHourAgo'")) $count = $friendMails->Count();
		}

		if(EmailAFriendExtension::$sender_name) {
			$mailFrom = EmailAFriendExtension::$sender_name;
			if(EmailAFriendExtension::$sender_email_address) $mailFrom .= ' <' . EmailAFriendExtension::$sender_email_address . '>';
		}
		else if(EmailAFriendExtension::$sender_email_address) $mailFrom = EmailAFriendExtension::$sender_email_address;
		else $mailFrom = 'Unknown Sender';

		foreach($toList as $index => $to) {
			if(! EmailAFriendExtension::$max_message_phour_pip || $count < EmailAFriendExtension::$max_message_phour_pip) {
				$friendEmail = new FriendEmail();
				$friendEmail->To = $to;
				$friendEmail->Message = $data['Message'];
				$friendEmail->From = $data['YourMailAddress'] ? $data['YourMailAddress'] : 'Unknown Sender';
				$friendEmail->IPAddress = $ip;
				$friendEmail->PageID = $data['PageID'];
				$friendEmail->write();
				$subject = EmailAFriendExtension::$mail_subject ? EmailAFriendExtension::$mail_subject : '';
				$subject .= ' | sent by '.$data['YourMailAddress'];
				$count++;
				$email = new Email(
					$mailFrom,
					$to,
					$subject,
					Convert::raw2xml($data['Message']) . '<br/><br/>Page Link : ' . $pageLink. '<br /><br />Sent by: '.$data['YourMailAddress']
				);
				$email->send();
			}
			else {
				$stopIndex = $index;
				break;
			}
		}

		if(count($toList) > 0) {
			$content = '';
			$endIndex = isset($stopIndex) ? $stopIndex : count($toList);
			if(! isset($stopIndex) || $stopIndex > 0) {
				$content .= '<p class="message good">This page has been successfully emailed to the following addresses :</p><ul>';
				for($i = 0; $i < $endIndex; $i++) $content .= '<li>' . $toList[$i] . '</li>';
				$content .= '</ul>';
			}
			if($endIndex < count($toList)) {
				$content .= '<p class="message required">This page could not be emailed to the following addresses :</p><ul>';
				for($i = $endIndex; $i < count($toList); $i++) $content .= '<li>' . $toList[$i] . '</li>';
				$content .= '</ul>';
			}
		}
		else $content = '<p class="message required">This page has not been emailed to anyone.</p>';

		$content .= '<br/><p>Click <a href="' . $this->controller->Link() . '">here</a> to go back to the previous page.</p>';

		$page = $this->controller;
		$page->Content = $content;

	  	return $page->renderWith('Page', 'Page_emailsent');
	}
}

