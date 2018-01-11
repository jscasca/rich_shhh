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
//$key = "my random key";
// random 128-bit initialization vector
/*$iv = openssl_random_pseudo_bytes(16);
// configure GCM object with AES-192 cipher and 13-bytes long authentication tag
$gcm = new GCM(new AES192Cipher(), 13);
// encrypt and generate authentication tag
list($ciphertext, $auth_tag) = $gcm->encrypt($plaintext, "", $key, $iv);
// print the ciphertext along with the authentication tag
$plain = $gcm->decrypt($ciphertext, $auth_tag, "", $key, $iv);*/
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
}
?>