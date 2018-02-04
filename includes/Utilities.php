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
		return $content != "" && is_string($content) && is_array(json_decode($content, true));
	}

	public static function validateLuckyNumber($lucky) {
		return $lucky != "";
	}

	public static function validateSecret($secret) {
		return $secret != "";
	}

	public static function validateFragments($fragments) {
		return is_array($fragments) && sizeOf($fragments)>0;
	}

	public static function extractExtras($extras) {
		$parties = json_decode($extras, true);
		return array($parties['trustees'], $parties['witnesses']);
	}

	public static function getFragments($request) {
		$fromString = json_decode($request, true);
		if(is_array($fromString)) return $fromString;
		else return array($request);
	}

	public static function validateExtras($extras) {
		$parties = json_decode($extras, true);
		return isset($parties['witnesses']) && isset($parties['trustees']) && sizeOf($parties['witnesses']) > 0 && sizeOf($parties['trustees']) > 0;
		//return $extras != "" && is_string($extras && is_array(json_decode($extras, true)));
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

	public static function generateTokenQuartet($n) {
		return bin2hex(openssl_random_pseudo_bytes($n));
	}

	public static function generateWitnessIv($token, $n) {
		$witnessesKeys = [];
		$tokenMap = [];
		$tokenBits = str_split($token, 4);
		for($i = 0; $i < sizeOf($tokenBits); $i++) {
			for($j = 0; $j < $n; $j++) {
				$size = strlen($tokenBits[$i]);
				if($size != 1) {
					$next = self::insertInScramble($tokenBits[$i], self::generateTokenQuartet($size - 1));
					while(isset($tokenMap[$i][$next])) {
						$next = self::insertInScramble($tokenBits[$i], self::generateTokenQuartet($size - 1));
					}
					$tokenMap[$i][$next] = $next;
				} else {
					$next = $tokenBits[$i];
				}
				$witnessesKeys[$j][$i] = $next; 
			}
		}
		foreach($witnessesKeys as $key=>$val) {
			$keyMap[] = implode('',$witnessesKeys[$key]);
		}
		return $keyMap;
	}

	public static function insertInScramble($code, $padding) {
		$chars = str_split($padding);
		$where = random_int(0,5);
		array_splice($chars, $where, 0, $code);
		return implode('', $chars);
	}

	public static function getWitnessIv($list) {
		if(sizeOf($list) < 2) {
			return $list[0];
		}
		$shards = str_split($list[0], 10);
		$base = str_split($list[1], 10);
		$key = "";
		foreach($shards as $i => $shard) {
			var_dump($shard);
			var_dump($i);
			$len = strlen($shard);
			var_dump($len);
			switch($len) {
				case 10: $size = 4; break;
				case 7: $size = 3; break;
				case 4: $size = 2; break;
				case 1: $size = 1; break;
				default: throw new Error('Weird key');
			}
			for($j = 0; $j < ($len + 1) - $size; $j++) {
				$buff = substr($shard, $j, $size);
				if(strpos($base[$i], $buff) !== false) {
					$key.=$buff;
					break;
				}
			}
		}
		return $key;
	}

	public static function getClaimExpiration() {
		return strtotime('+30 days');
	}

	public static function getExpirationDate() {
		$dt = strtotime('+1 hour');
		return $dt;
	}

	public static function getLongExpirationDate() {
		return strtotime('+48 hours');
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

	public static function encodeTrustees($id, $trustees) {
		$encoded = [];
		foreach($trustees as $trustee) {
			$encoded[] = self::getTrusteeHash($id, $trustee);
		}
		return $encoded;
	}

	public static function getTrusteeHash($id, $trustee){
		return md5($id . $trustee);
	}
}
?>