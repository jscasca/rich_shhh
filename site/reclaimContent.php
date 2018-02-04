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

function gone() {
	header("HTTP/1.1 410 Gone");
	die();
}

session_start();


$con = new Congo();

if(!isset($_REQUEST['i']) || !isset($_REQUEST['code'])) {
	header("HTTP/1.1 404 Not Found");
	die();
}

$key = $_REQUEST['i'];
$iv = $_REQUEST['code'];

$secret = $_REQUEST['secret'];
if(!Util::validateSecret($secret)) {
	fail();
}

$fragments = Util::getFragments($_REQUEST['fragments']);
if(!is_array($fragments)) {
	$fragments = array($fragments);
}
if(!Util::validateFragments($fragments)) {
	fail();
}

$fragmentedKey = Util::getWitnessIv($fragments);
 //must be an array in json
$q = $con->query("ready", array("id"=>$key));

if($q->hasResults()) {
	//
	$locallyEncrypted = hex2bin($q->getFirst()->content);
	try {
		$locallyDecrypted = hex2bin(Util::decrypt($locallyEncrypted, $key, $iv));
	} catch(Exception $e) {
		session_destroy();
		header("HTPP/1.1 401 Unauthorized");
		die();
	}

	try {
		//
		$finally = Util::decrypt($locallyDecrypted, $secret, $fragmentedKey);
	} catch(Exception $e) {
		//session_destroy();
		//Give them a second chance
		header("HTPP/1.1 403 Forbidden");
		die();
	}
	header("Content-Type: application/json");
	header("HTTP/1.1 200 OK");
	echo $finally;
} else {
	//not found
	//log stuff here
	gone();
}
//Validate


?>