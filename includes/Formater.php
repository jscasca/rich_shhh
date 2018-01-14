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

class DuplicateFormater {
	private $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function subject() {
		return "Failed to store: duplicate resource";
	}
	public function plainContent() {
		return "The name of the resource is duplicated for this user";
	}
	public function htmlContent() {
		return "The name of the resource is duplicated for this user";
	}
}

class StoredFormater {
	private $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function subject() {
		return "Stored properly";
	}
	public function plainContent() {
		return "The resource $name was stored properly";
	}
	public function htmlContent() {
		return "The resource <b>$name</b> was stored properly";
	}
}

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
?>