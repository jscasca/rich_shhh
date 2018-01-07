<?php

require('Mailer.php');
//do all sorts of crazy stuff
$user = $_REQUEST['user']; //User at this stage must be an email address
$name = $_REQUEST['name']; //Name is the name of the contents being stored
$content = $_REQUEST['content']; //Content is the encrypted content to be stored

//[OPTIONAL] Optional stuff will be configured at a later point
//Trustees: Must have an email and a piece of code
//3rd Party: Must have an email and a piece of code

//get the id = hash( user + name)
$id = md5($user . $name);

//look for the element in db
$conn = new Mongo();
$db = $conn->shhh;
$collection = $db->stored;
$q = $collection->find({"id":$id});
$qCount = $q->count();

if($qCount > 0 ) {
	//there is at least one doc
	//Notify by email that it is already taken
} else {
	//Nothing found
	$document = array( //Create a new doc
		"id" => $id,
		"content" => $content
	);
	$collection->insert($document); //Store the info
	//Notify the user by email
}

//If trustess and 3rdparties they need to go her
$conn->close();

// if found email them that it failed

//if not found store this new one and email them that 
?>