<?php
//MAKE A CLAIM
header('Access-Control-Allow-Origin: http://localhost:3000', false);
header('Access-Control-Allow-Methods: POST,GET', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
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

// use the name and your email to make a claim
$user = $_REQUEST['user']; //User at this stage must be an email address
if(!Util::validateUser($user)) { //Must be email (phone in the future)
	fail();
}
$name = $_REQUEST['name']; //Name is the name of the contents being stored
if(!Util::validateResourceName($name)) { //Must not be empty
	fail();
}
$trustee = $_REQUEST['trustee']; //Name is the name of the contents being stored
if(!Util::validateUser($trustee)) { //Must not be empty
	fail();
}

$id = Util::getId($user, $name);
$dbTrustee = Util::getTrusteeHash($id, $trustee);

$con = new Congo();

$q = $con->query("claimable", array("id"=>$id));

if($q->hasResults()) {
	$r = $q->getFirst();
	//validate the trustee is in the list
	$allowedTrustees = $r->trustees;
	if(!in_array($dbTrustee, $allowedTrustees)) {
		//You are not on the allowed list
		gone(); //log about this issue
	}
	//Move to pending
	$code = Util::generateLocalKey(32);
	//Loop querying for existing code
	$expires = Util::getClaimExpiration();
	$document = array(
		"id"=>$code,
		"content"=>$r->content,
		"cooldown"=>$expires,
		"trustee"=>$trustee);
	$con->insert("pending", $document);
	//Notify trustee
	$mailer = new Mailer();//Make a denial link here
	$link = "deny.php?code=".$code;
	$mailer->notifyOwnerAboutClaim($user, $name, $code, $link, $trustee);
	$mailer->notifyTrusteeAboutPending($trustee, $name);
	//Notify owner
} else {
	//Resource does not exist
	gone(); //log and notify if needed
}

header("HTTP/1.1 200 OK");

// ask the owner for the kill switch signal

// let them know the signal has been sent and they will be notified in a month

?>