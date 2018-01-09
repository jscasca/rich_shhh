<?php

class Util {
	
	public function __construct() {}

	public static function getId($user, $name) {
		return md5($user . $name);
	}

	public static function encrypt() {
		//do stuff
	}

	public static function decrypt() {
		//more stuff
	}

	public static function validateUser($user) {
		return filter_var($user, FILTER_VALIDATE_EMAIL);
	}

	public static function validateResourceName($resource) {
		return $resource != "";
	}

	public static function validateContent($content) {
		return $content != "";
	}

	public static function validateLuckyNumber($lucky) {
		return $lucky != "";
	}

	public static function validateSecret($secret) {
		return $secret != "";
	}

	public static function generatePassword($secret, $lucky, $length = 24) {
		//24 chars for 192-bits
	}

	public static function generateIV($lucky, $length = 16) {
		//16 chars for 128-bit
		return substr(hash('sha512', $lucky), 0, $length);
	}
}
?>