<?php

$debug = true;

define('MAX_ACCESS_LOG', 6);

require('../includes/Congo.php');
require('../includes/Mailer.php');
require('../includes/Utilities.php');
require('../vendor/autoload.php');

//do all sorts of crazy stuff
$user = $_REQUEST['user']; //User at this stage must be an email address
if(!Util::validateUser($user)) { //Must be email (phone in the future)
	fail();
}
$name = $_REQUEST['name']; //Name is the name of the contents being stored
if(!Util::validateResourceName($name)) { //Must not be empty
	fail();
}
$content = $_REQUEST['content']; //Content is not encrypted yet
if(!Util::validateContent($content)) { //Must be valid json
	fail();
}

$lucky = $_REQUEST['lucky'];
if(!Util::validateLuckyNumber($lucky)) {
	fail();
}
$secret = $_REQUEST['secret'];
if(!Util::validateSecret($secret)) {
	fail();
}

/**
IP validation for max retiress and user requests
*/
$time = Util::getCurrentTime();
$ip = Util::getIP($_SERVER);
$logEntry = array(
	"target"=>$user,
	"src"=>$ip,
	"time"=>$time,
	"site"=>'storage'
	);

$conn = new Congo();
$lastHour = Util::getLastHour();
$logUserQuery = $conn->query("access_log", 
	array(
		'time' => array('$gt' => $lastHour),
		'$or' => array('target' => $user, 'src' => $ip),
		'failed' => true
		));

if($logUserQuery->countResults() > MAX_ACCESS_LOG) {
	//mark as a failed attemp and die
	$logEntry['failed'] = true;
	$conn->insert("access_log", $logEntry);
	header()
	die();
}
//$con->delete("accessible", array("expires"=>array('$lt'=>$expired)));

//[OPTIONAL] Optional stuff will be configured at a later point
//Trustees: Must have an email and a piece of code
//3rd Party: Must have an email and a piece of code

//get the id = hash( user + name)
$id = Util::getId($user, $name);

//look for the element in db
$q = $conn->query("stored",array("id"=>$id));

$mailer = new Mailer();

if($q->hasResults()) {
	//there is at least one doc
	//Notify by email that it is already taken
	$mailer->notifyFailedStorage($user, $name);
} else {
	//Get the trustees here and loop iver them. Add them in the notification email 
	$extras = $_REQUEST['extras'];

	$encrypted = Util::encrypt($content, $secret, $lucky); //Encrypt content
	$document = array(
		"id" => $id,
		"content" => bin2hex($encrypted)
	); //Prepare the document to store
	$conn->insert("stored", $document); //Store the original in db.stored
	if(Util::validateExtras($extras)) {
		//There are extras
		list($trustees, $witnesses) = Util::extractExtras($extras);
		//Prepare a new key for all the trustees and 3rd parties
		$key = Util::generateLocalKey();
		$iv = Util::generateLocalIv();
		//
		$extraEncrypted = Util::encrypt($content, $key, $iv); // Encrypt the content for the trustees

		$encodedTrustees = Util::encodeTrustees($id, $trustees); //Hash the trustees to store in db

		$claimableDoc = array(
			"id" => $id,
			"content" => bin2hex($extraEncrypted),
			"trustees" => $encodedTrustees
			); // Prepare document to store as claimable
		$conn->insert("claimable", $claimableDoc); //Store the claimable doc in db.claimable

		//store the new encrypted doc in a new table
		$mailer->notifySuccessfulStorageWithTrustees($user, $name, $trustees, $witnesses);
		$mailer->notifyTrustees($trustees, $user, $name, $key);
		if(sizeOf($witnesses) < 2) {
			//dont break keys just send one
			$mailer->notifyWitness($witnesses[0], $user, $name, $iv);
		} else {
			$ivs = Util::generateWitnessIv($iv,sizeOf($witnesses));
			foreach($witnesses as $i=>$witness) {
				$mailer->notifyWitness($witness, $user, $name, $ivs[$i]);
			}
			//break keys
		}
	} else {
		$mailer->notifySuccessfulStorage($user, $name);
	}
}
header("HTTP/1.1 200 OK");
//header("HTTP/1.1 404 Not Found");
//die();

function fail() {
	header("HTTP/1.1 400 Bad Request");
	die();
}

function failSilent() {
	header("HTTP/1.1 200 OK");
	die();
}
?>