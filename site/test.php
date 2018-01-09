<?php

use Sop\GCM\GCM;
use Sop\GCM\Cipher\AES\AES192Cipher;

require('../includes/Utilities.php');
require('../vendor/autoload.php');
/*
$text = "Some random content here to test";
$add = "user";
$key = "som random key";
$iv = openssl_random_pseudo_bytes(12);

list($cipher, $tag) = AESGCM::encrypt($text, $add, $key, $iv);
*/
//echo bin2hex($cipher)."<br>".bin2hex($tag)."<br>".bin2hex($iv);

//from command line 
//openssl enc -aes-192-cbc -k secret -P
echo Util::generateIV(29);
echo "<br>";
$plaintext = "Some random super long paragraph used in some long and long cases";
// 192-bit encryption key
$key = "012345678901234567890123";
//$key = "my random key";
// random 128-bit initialization vector
$iv = openssl_random_pseudo_bytes(16);
// configure GCM object with AES-192 cipher and 13-bytes long authentication tag
$gcm = new GCM(new AES192Cipher(), 13);
// encrypt and generate authentication tag
list($ciphertext, $auth_tag) = $gcm->encrypt($plaintext, "", $key, $iv);
// print the ciphertext along with the authentication tag
$plain = $gcm->decrypt($ciphertext, $auth_tag, "", $key, $iv);
// and the initialization vector
echo bin2hex($ciphertext) . "<br>" . bin2hex($auth_tag) . "<br>" . bin2hex($iv) . "<br>" . $plain .
     "<br>";

?>