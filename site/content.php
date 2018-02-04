<?php

header('Access-Control-Allow-Origin: http://localhost:3000', false);
header('Access-Control-Allow-Methods: POST,GET', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
require('../includes/Congo.php');
require('../includes/Mailer.php');
require('../includes/Utilities.php');
require('../vendor/autoload.php');

function fail() {
	header("HTTP/1.1 400 Bad Request");
	die();
}

if(!isset($_REQUEST['i']) || !isset($_REQUEST['code'])) {
	header("HTTP/1.1 404 Not Found");
	die();
}
/*
if($_SESSION['counter'] !== 0) {
	session_destroy();
	header("HTTP/1.1 403 Forbidden");
	die();
}*/

$key = $_REQUEST['i'];
$iv = $_REQUEST['code'];

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
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	try {
		$finally = Util::decrypt($locallyDecrypted, $secret, $lucky);
	} catch(Exception $e) {
		//if this fails the user/pass was incorrect and we might give him another chance
		//Give them a second chance
		header("HTPP/1.1 403 Forbidden");
		die();
	}
	header("Content-Type: application/json");
	header("HTTP/1.1 200 OK");
	echo $finally; //Print the json string
	die();
	//var_dump($finally);
	//$content = (array)json_decode($finally);
	//var_dump($content);


} else {
	//figure how to let him know the resource does not exist
	header("HTTP/1.1 410 Gone");
}
session_destroy();

//header("HTTP/1.1 404 Not Found");
//die();

?>