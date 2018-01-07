<?php

class Formater {

	private static $base = "";
	
	public function __construct() {
		//
	}

	public static format() {
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