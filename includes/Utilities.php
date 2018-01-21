<?php

require('../vendor/autoload.php');

use Sop\GCM\GCM;
use Sop\GCM\Cipher\AES\AES192Cipher;

class Util {
	
	public function __construct() {}

	public static function getId($user, $name) {
		return md5($user . $name);
	}

	public static function encrypt($content, $secret, $lucky) {
		$gcm = new GCM(new AES192Cipher(), 13);
		list($ciphertext, $auth_tag) = $gcm->encrypt($content, "", self::getKey($secret, $lucky), self::getIv($lucky));
		return $ciphertext . ":" . $auth_tag;
	}

	public static function decrypt($content, $secret, $lucky) {
		list($ciphertext, $auth_tag) = self::contentAndTag($content);
		$gcm = new GCM(new AES192Cipher(), 13);
		return $gcm->decrypt($ciphertext, $auth_tag, "", self::getKey($secret, $lucky), self::getIv($lucky));
	}

	public static function validateUser($user) {
		return filter_var($user, FILTER_VALIDATE_EMAIL);
	}

	public static function validateResourceName($resource) {
		return $resource != "";
	}

	public static function validateContent($content) {
		return $content != "" && is_string($content && is_array(json_decode($content, true)));
	}

	public static function validateLuckyNumber($lucky) {
		return $lucky != "";
	}

	public static function validateSecret($secret) {
		return $secret != "";
	}

	public static function validateExtras($extras) {
		return false;
	}

	public static function getKey($secret, $lucky, $length = 24) {
		//24 chars for 192-bits
		return substr(md5($lucky.$secret.$lucky.$secret), 0, $length);
	}

	public static function getIv($lucky, $length = 16) {
		//16 chars for 128-bit
		return substr(hash('sha512', $lucky), 0, $length);
	}

	public static function contentAndTag($encrypted) {
		$pos = strrpos($encrypted, ":", -1);
		$content = $encrypted;
		$tag = "";
		if($pos) {
			$tag = substr($content, $pos + 1);
			$content = substr($content, 0, $pos);
		}
		return array($content, $tag);
	}

	public static function generateLocalKey($length = 24) {
		$key = openssl_random_pseudo_bytes($length);
		return bin2hex($key);
	}

	public static function generateLocalIv($length = 16) {
		// random 128-bit initialization vector
		$iv = openssl_random_pseudo_bytes($length);
		return bin2hex($iv);
	}

	public static function getExpirationDate() {
		$dt = strtotime('+1 hour');
		return $dt;
	}

	public static function getCurrentTime() {
		return strtotime('now');
	}

	public static function getLastHour() {
		return strtotime('-1 hour');
	}

	public static function getIP($server) {
		if(!empty($server['HTTP_CLIENT_IP'])) {
			$ip = $server['HTTP_CLIENT_IP'];
		} else if(!empty($server['HTTP_X_FORWARDED_FOR'])) {
			$ip = $server['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $server['REMOTE_ADDR'];
		}
		return $ip;
	}
}
?>