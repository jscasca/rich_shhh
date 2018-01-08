<?php

if(!isset($server) || !$server) {
	header("HTTP/1.1 404 Not Found");
	die();
}

class Formater {

	private static $base = "";
	
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
		return "Resource stored";
	}

	public static function storedResourcePlainContent($name) {
		return "";
	}

	public static function storedResourceHtmlContent($name) {
		return "";
	}
}
?>