<?php

$server = true;

require('Utilities.php');

//get the user and name
$user = $_REQUEST['user'];
$name = $_REQUEST['name'];

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
	//Nothing found
	//Notify the user by email: Someone tried accessing this resource that does not exist
}

$conn->close();

//let the user know
header("Location: retrieved.html");
?>