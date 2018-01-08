<?php

if(!isset($server) || !$server) {
	header("HTTP/1.1 404 Not Found");
	die();
}

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
}
?>