<?php

require('Utilities.php');

$user = $_REQUEST['user']; //User at this stage must be an email address
if(!Util::validateUser($user)) { //Must be email (phone in the future)
	fail();
}
$name = $_REQUEST['name']; //Name is the name of the contents being stored
if(!Util::validateResourceName($name)) { //Must not be empty
	fail();
}

$id = Util::getId($user, $name);

// hash it and query the item
$conn = new Mongo();
$db = $conn->shhh;

// Look for db of entries!! Check if this session has many attempts
$access = $db->access; // DB: access stores the client information
// TODO: think of how to check for user sessions
$collection = $db->stored;
$q = $collection->find({"id":$id});
$qCount = $q->count();

if($qCount > 0 ) {
	//there is at least one doc
	//Preapre a new code
	$code = Util::getUniqueCode();
	//Notify by email and prepare by inserting in the new db
	$accesible = $db->accesible;
	$document = array(
		//
		"id" => $thecodewejustmadeup,
		"content" => $contentqueried,
		"expires" => $anhourfromnow
	);
	$accesible->insert($document);
	//Notify the user that their resource is ready to access and give them the new code
} else {
	$mailer->notifyMissingRetrieval($user, $name);
}

$conn->close();

//let the user know
header("Location: retrieved.html");
?>