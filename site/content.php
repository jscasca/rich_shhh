<?php

require('../includes/Congo.php');
require('../includes/Mailer.php');
require('../includes/Utilities.php');
require('../vendor/autoload.php');
session_start();

if(!isset($_SESSION['i']) || !isset($_SESSION['code']) || !isset($_SESSION['counter'])) {
	session_destroy();
	header("HTTP/1.1 404 Not Found");
	die();
}
/*
if($_SESSION['counter'] !== 0) {
	session_destroy();
	header("HTTP/1.1 403 Forbidden");
	die();
}*/

$key = $_SESSION['i'];
$iv = $_SESSION['code'];

$lucky = $_REQUEST['lucky']; 
if(!Util::validateLuckyNumber($lucky)) { 
	fail();
}
$secret = $_REQUEST['secret'];
if(!Util::validateSecret($secret)) { 
	fail();
}

$conn = new Congo();
$q = $conn->query("accessible",array("id"=>$key));

if($q->hasResults()) {
	//found time to decrypt
	$locallyEncrypted = hex2bin($q->getFirst()->content);
	try {
		$locallyDecrypted = hex2bin(Util::decrypt($locallyEncrypted, $key, $iv));
	} catch(Exception $e) {
		//If this fails the resource was not valid
		session_destroy();
		header("HTTP/1.1 401 Forbidden");
		die();
	}

	try {
		$finally = Util::decrypt($locallyDecrypted, $secret, $lucky);
	} catch(Exception $e) {
		//if this fails the user/pass was incorrect and we might give him another chance
		header("Location: validate.php");
		die();
	}
	var_dump($finally);
	$content = (array)json_decode($finally);
	var_dump($content);


} else {
	//figure how to let him know the resource does not exist
	echo "The resource you are trying to access has expired";
}
session_destroy();


function fail() {
	header("Location: 500.html");//Throw 500 error
	die();
}

?>