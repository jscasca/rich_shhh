<?php

define("BASE","localhost/rich_shhh/site");

class Formater {

	private static $base = "localhost/rich_shhh/site";
	
	public function __construct() {
		//
	}

	public static function duplicateResourceSubject() {
		return "Failed to store: duplicate resource";
	}

	public static function duplicateResourcePlainContent($name) {
		return "";
	}

	public static function duplicateResourceHtmlContent($name) {
		return "";
	}

	public static function storedResourceSubject() {
		return "Resource stored successfuly";
	}

	public static function storedResourcePlainContent($name) {
		return "The resource $name has been stored successfuly";
	}

	public static function storedResourceHtmlContent($name) {
		return "<p>The resource <b>$name</b> has been stored succesfuly!</p><br><p>Access <a href='http://$base/retrieve.html'>Link</a> to access it</p>";
	}

	public static function missingResourceSubject($name) {
		return "Missing resource";
	}

	public static function missingResourcePlainContent($name) {
		return "The resource $name does not exist for this user. If you did not tried to access this resource please notify the admin";
	}

	public static function missingResourceHtmlContent($name) {
		return "<p>The resource <b>$name</b> does not exist for this user</p>";
	}

	public static function resourceReadySubject() {
		return "";
	}
}

//Notify user that the info he tried to store already exists
class DuplicateFormater {
	private $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function subject() {
		return "Failed to store: duplicate resource";
	}
	public function plainContent() {
		return "The name [$this->name] is already used for another resource";
	}
	public function htmlContent() {
		return "The name [<b>$this->name</b>] is already used for another resource";
	}
}

//Notify User that the storage was successful
class StoredFormater {
	private $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function subject() {
		return "Stored properly";
	}
	public function plainContent() {
		return "The resource $this->name was stored properly";
	}
	public function htmlContent() {
		return "The resource <b>$this->name</b> was stored properly";
	}
}

//Notify user that someone tried to access that resource
class MissingFormater {
	private $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function subject() {
		return "Missing resource";
	}
	public function plainContent() {
		return "The resource $this->name that you tried to access does not exist.";
	}
	public function htmlContent() {
		return "The resource <b>$this->name</b> that you tried to access does not exist.";
	}
}

//Notify user that the resource is ready to be retrieve at the attached link
class ReadyFormater {
	private $name;
	private $link;
	public function __construct($name, $link) {
		$this->name = $name;
		$this->link = $link;
	}
	public function subject() {
		return "Resource is ready to access";
	}
	public function plainContent() {
		return "The resource is ready to access at $this->link";
	}
	public function htmlContent() {
		return "The resource is ready to access at <a href='".BASE."/$this->link'>link</a>";
	}
}

//Notify User that the storage was successful with a list of trustees and witnesses
class StoredWithExtrasFormater {
	private $name;
	public function __construct($name, $trustees, $witnesses) {
		$this->name = $name;
	}
	public function subject() {
		return "Your information has been secured!";
	}
	public function plainContent() {
		return "The resource $this->name was stored properly";
	}
	public function htmlContent() {
		return "The resource <b>$this->name</b> was stored properly";
	}
}

//Notify the witness that he is used to store a resource
class NotifyWitnessFormater {
	private $name;
	private $user;
	private $iv;
	public function __construct($user, $name, $iv) {
		$this->name = $name;
		$this->user = $user;
		$this->iv = $iv;
	}
	public function subject() {
		return "You have been selected as a trusted party";
	}
	public function plainContent() {
		return "You have been selected as a trusted party by $user. To provide access to [$name] use the storage key [$iv]";
	}
	public function htmlContent() {
		return "You have been selected as a trusted party by $user. To provide access to [<b>$name</b>] use the storage key [<b>$iv</b>]";
	}
}

//Notify the witness that he is used to store a resource
class NotifyTrusteeFormater {
	private $name;
	private $user;
	private $key;
	public function __construct($user, $name, $key) {
		$this->name = $name;
		$this->user = $user;
		$this->key = $key; //Make a link here to make a claim (claim can't be accesed by the website)
	}
	public function subject() {
		return "You have been selected as a trustee";
	}
	public function plainContent() {
		return "You have been selected as a trustee by $user. To claim access to [$name] use the storage key [$key].";
	}
	public function htmlContent() {
		return "You have been selected as a trustee by $user. To claim access to [<b>$name</b>] use the storage key [<b>$key</b>]";
	}
}

//Notify user that the resource is ready to be retrieve at the attached link
class ClaimReadyFormater {
	private $name;
	private $link;
	public function __construct($link) {
		$this->name = $name;
		$this->link = $link;
	}
	public function subject() {
		return "Claim is ready to access";
	}
	public function plainContent() {
		return "Your claim has been successful. To finish your claim access the following url: $this->link";
	}
	public function htmlContent() {
		return "Your claim has been successful. To complete your claim access <a href='".BASE."/$this->link'>link</a>";
	}
}

//Notify a trustee that a claim is being processed. He will be notified in 30 days
class NotifyClaimPendingFormater {
	public function __construct() {
	}
	public function subject() {
		return "Your claim is being processed";
	}
	public function plainContent() {
		return "You will be notified once the claim has been processed";
	}
	public function htmlContent() {
		return "You will be notified once the claim has been processed";
	}
}

//Notify the owner that a claim has been properly denied
class NotifySuccessfulDenialFormater {
	private $code;
	public function __construct($code) {
		$this->code = $code;
	}
	public function subject() {
		return "The claim [$this->code] has been denied";
	}
	public function plainContent() {
		return "The claim with reference [$this->code] has been denied.";
	}
	public function htmlContent() {
		return "The claim with reference [$this->code] has been denied.";
	}
}

//Notify the owner that a claim has been made and provide a link to deny that claim
class NotifyOwnerAboutClaimFormater {
	private $name;
	private $code;
	private $link;
	private $trustee;
	public function __construct($name, $code, $link, $trustee) {
		$this->name = $name;
		$this->code = $code;
		$this->link = $link;
		$this->trustee = $trustee;
	}
	public function subject() {
		return "A resource has been claimed by one of your trustees [$this->code]";
	}
	public function plainContent() {
		return "[$this->trustee] has claimed the resource [$this->name]. To deny the claim access the link [$this->link]. You have 30 days to deny this claim";
	}
	public function htmlContent() {
		return "[$this->trustee] has claimed the resource [<b>$this->name</b>]. To deny the claim access this <a href='$this->link'>link</a> or copy this into your browser [$this->link]. You have 30 days to deny this claim";
	}
}

// Claim has been denied
// Let the claimer know that the claim has been denied by the owner of the resource
class NotifyClaimDenialFormater {
	public function __construct() {
	}
	public function subject() {
		return "Your claim has been denied";
	}
	public function plainContent() {
		return "The owner of the content has denied your claim.";
	}
	public function htmlContent() {
		return "The owner of the content has denied your claim.";
	}
}

// Claim has been started
// Let the claimer know that there is a 30 days cool down period
class NotifyTrusteeClaimStartedFormater {
	public function __construct() {
	}
	public function subject() {
		return "A claim is being processed";
	}
	public function plainContent() {
		return "Your claim is being processed. The owner of the resource has 30 days to deny this claim. If the claim is not denied you will receive another email with further instructions";
	}
	public function htmlContent() {
		return "Your claim is being processed. The owner of the resource has 30 days to deny this claim. If the claim is not denied you will receive another email with further instructions";
	}
}
?>