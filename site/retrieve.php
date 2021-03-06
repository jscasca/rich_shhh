<?php

require('../includes/Mailer.php');
require('../includes/Congo.php');
require('../includes/Utilities.php');

$user = $_REQUEST['user']; //User at this stage must be an email address
if(!Util::validateUser($user)) { //Must be email (phone in the future)
	fail();
}
$name = $_REQUEST['name']; //Name is the name of the contents being stored
if(!Util::validateResourceName($name)) { //Must not be empty
	fail();
}

$id = Util::getId($user, $name);

$mailer = new Mailer();

// hash it and query the item
$conn = new Congo();
$q = $conn->query("stored",array("id"=>$id));

if($q->hasResults()) {
	//there is at least one doc
	//retrieve the doc
	$r = $q->getFirst();
	$key = Util::generateLocalKey();
	$iv = Util::generateLocalIv();
	$encrypted = Util::encrypt($r->content, $key, $iv);
	$expires = Util::getExpirationDate();
	//Notify by email and prepare by inserting in the new db
	$document = array(
		//
		"id" => $key,
		"content" => bin2hex($encrypted),
		"expires" => $expires
	);
	$conn->insert("accessible", $document);
	//Notify the user that their resource is ready to access and give them the new code
	$link = "validate.php?i=$key&code=$iv";
	$mailer->notifyResourceReady($user, $name, $link);
} else {
	$mailer->notifyMissingRetrieval($user, $name);
}

//let the user know
header("Location: retrieved.html");
?>