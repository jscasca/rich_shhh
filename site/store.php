<?php

$debug = true;

require('../includes/Mailer.php');
require('../includes/Utilities.php');
require('../vendor/autoload.php');
var_dump($_REQUEST);
die();
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

$luckyNumber = $_REQUEST['lucky'];
if(!Util::validateLuckyNumber($luckyNumber)) {
	fail();
}
$secret = $_REQUEST['secret'];
if(!Util::validateSecret($secret)) {
	fail();
}

//[OPTIONAL] Optional stuff will be configured at a later point
//Trustees: Must have an email and a piece of code
//3rd Party: Must have an email and a piece of code

//get the id = hash( user + name)
$id = Util::getId($user, $name);

//look for the element in db
$conn = new Mongo();
$db = $conn->shhh;
$collection = $db->stored;
//$q = $collection->find({"id":$id});
$q = $collection->find(array("id"=>$id));
$qCount = $q->count();

$mailer = new Mailer();


if($qCount > 0 ) {
	//there is at least one doc
	//Notify by email that it is already taken
	$mailer->notifyFailedStorage($user, $name);
} else {
	//Nothing found

	//Get the trustees here and loop iver them. Add them in the notification email 
	$extras = $_REQUEST['extras'];

	$encrypted = Util::encrypt($content, $secret, $lucky);
	if(Util::validateExtras($extras)) {
		//There are extras
		//Prepare a new key for all the trustees and 3rd parties
		//store the new encrypted doc in a new table
	} else {
		$document = array(
			"id" => $id,
			"content" => $encrypted
		);
		$collection->insert($document);
		$mailer->notifySuccessfulStorage($user, $name);
	}
}

//If trustess and 3rdparties they need to go here
$conn->close();

// if found email them that it failed

//if not found store this new one and email them that 
header("Location: stored.html");

function fail() {
	header("Location: 500.html");//Throw 500 error
	die();
}
?>