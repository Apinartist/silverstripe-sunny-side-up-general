<?php
/**
 * This class will allow you to obfusticate emails addresses, instead of doing
 * something fancy but easy-to-break with Javascript, it will instead allow you
 * to link to the HideEmail_Controller below, which will in turn figure out the
 * email address to send to, and redirect the user using the Location header.
 *
 * Thanks to Colin Viebrock for the inspiration: http://viebrock.ca/code/11/
 *
 * Note that the link between HideEmail_Controller that does the processing and
 * the /mailto/ link on the site is specified in the _config.php file, take a
 * look there to see how SilverStripe can rewrite controller URLs. :)
 *
 * There are a number of ways to invoke this, depending on your circumstances.
 * Here's some examples:
 *
 * - I want to show all the members registered on my site, along with their
 * email addresses:
 *   * In your template code, simply add $HideEmailLink. For example:
 *     <a href="$HideEmailLink" title="Email $FirstName">Email $FirstName</a>
 *     <a href="$HideEmailLink/hi!">Email $FirstName</a> - adding a subject
 *
 * - I want to link to myself in the middle of some content on my site:
 *   * In the CMS, create a link to 'another website', and enter the
 *     following in the 'URL' box (to email to matt@silverstripe.com with the
 *     subject 'Hi'):
 *     'mailto/matt/silverstripe.com/Hi' (don't include the single quotes)
 *
 * Note: In the future I hope to extend the second case, so that if you choose
 * the 'email' option, it will automatically generate a obfusticated email.
 *
 * This is licensed under the WTFPL - the Do What The Fuck You Want To public
 * license, available in the COPYING file. The latest version is always
 * available at http://sam.zoy.org/wtfpl/COPYING
 *
 * @package hidemailto
 * @author Matt Peel <matt@silverstripe.com>
 */

/**
 * The DataObjectDecorator that adds $HideEmailLink functionality to the Member
 * class.
 */
class HideEmail_Role extends DataObjectDecorator {
	function HideEmailLink() {
		return "mailto/" . $this->owner->ID;
	}
}
/**
 * Generates obfusticated links, and also holds the method called when /mailto/
 * is called via the URL. As noted above, take a look at the _config.php file to
 * see how mailto/ maps to this class.
 */
class HideEmail_Controller extends ContentController {
	/**
	 * The list of allowed domains to create a mailto: link to. By default, allow
	 * all domains.
	 *
	 * TODO Maybe the default should be to allow the current domain only?
	 */
	static $allowed_domains = '*';

	/**
	 * @param mixed $domains Either an array of domains to allow, or the string
	 * '*' to allow all domains.
	 */
	static function set_allowed_domains($domains) {
		self::$allowed_domains = $domains;
	}

	var $url = '';

	/**
	 * This is called by default when this controller is executed.
	 */
	function index() {
		// We have two situations to deal with, where urlParams['Action'] is an int (assume Member ID), or a string (assume username)
		if(is_numeric(Director::urlParam('Action'))) {
			// Action is numeric, assume it's a member ID and optional ID is the email subject
			$member = DataObject::get_by_id('Member', (int)Director::urlParam('Action'));
			if(!$member) user_error("No member found with ID #" . Director::urlParam('Action'), E_USER_ERROR); // No member found with this ID, perhaps we could redirect a user back instead of giving them a 500 error?

			list($user, $domain) = explode('@', $member->Email);
			$subject = Director::urlParam('ID');
		}
		else {
			// Action is not numeric, assume that Action is the username, ID is the domain and optional OtherID is the email subject
			$user = Director::urlParam('Action');
			$domain = Director::urlParam('ID');
			$subject = Director::urlParam('OtherID');
		}

		// Make sure the domain is in the allowed domains
		if((is_string(self::$allowed_domains) && self::$allowed_domains == '*') || in_array($domain, self::$allowed_domains)) {
			// Create the redirect
			$target = 'mailto:' . $user . '@' . $domain;

			if(isset($subject)) $target .= '?subject=' . Convert::raw2xml($subject);
			header("Location: " . $target);
			// This is a bit hacky and can probably be tidied up a lot...
			if(isset($_SERVER['HTTP_REFERER'])) {
				$this->redirectBackURL = $_SERVER['HTTP_REFERER'];
			}
			if(!$this->redirectBackURL) {
				$this->redirectBackURL = Director::absoluteBaseURL();
			}
			echo
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>sending email ...</title>
	<meta http-equiv="refresh" content="3000; url='.$this->redirectBackURL.'" />
	<script type="text/javascript">setTimeout(\'window.location.href = "'.$this->redirectBackURL.'"\', 3000);</script>
 </head>
<body>
	<h2>Sending Email ...</h2>
	<a href="'.$this->redirectBackURL.'">please click to continue to '.$this->redirectBackURL.'</a>
</body>
</html>		';
		}
		else {
			user_error("We're not allowed to redirect to the domain '$domain', because it's not listed in the _config.php file", E_USER_ERROR);
		}
		die();
	}

	/**
	 * Calls the default action for this class - index() - when it can't find the
	 * action specified by Director::urlParam('Action']
	 */
	function defaultAction() {
		return $this->index();
	}

	function RedirectBackURL () {
		return $this->redirectBackURL;
	}

	/**
	 * To satisfy pageless-controller logic.
	 * TODO Extend PagelessController once that's merged into the core
	 */
	function __construct($dataRecord = null) {
		$this->index();
		parent::__construct($dataRecord);
	}
}
?>