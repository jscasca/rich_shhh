<?php
//MAKE A CLAIM
define('MAX_ACCESS_LOG', 6);

require('../includes/Mailer.php');
require('../includes/Congo.php');
require('../includes/Utilities.php');

function fail() {
	header("HTTP/1.1 500 Server Error");
	die();
}

function gone() {
	header("HTTP/1.1 410 Gone");
	die();
}

$code = $_REQUEST['code']; //Name is the name of the contents being stored
if(!Util::validateCode($trustee)) { //Must not be empty
	fail();
}

$con = new Congo();

$q = $con->query("pending", array("id"=>$code));

if($q->hasResults()) {
	$r = $q->getFirst(); //either delete by _id or by code (id)
	//Move to pending
	$trustee = $r->trustee;
	//Trustee to notify
	$con->delete("pending", array("id"=>$code));
	//Notify trustee
	$mailer = new Mailer();
	$mailer->notifyOwnerAboutDenial($user, $code);
	$mailer->notifyTrusteeAboutDenial($trustee);
	//Notify owner
} else {
	//Resource does not exist
	gone(); //log and notify if needed
}

header("HTTP/1.1 200 OK");

// ask the owner for the kill switch signal

// let them know the signal has been sent and they will be notified in a month

?>