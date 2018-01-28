<?php

//TODO: define base and link base
require('../includes/Congo.php');
require('../includes/Mailer.php');

function processAndDelete($entry, $con) {
	//Each entry = { _id, id, $denyCode, $trusteeEmail, $content}
	//Get the entry
	$key = Util::generateLocalKey();
	$iv = Util::generateLocalIv();
	$encrypted = Util::encrypt($entry->content, $key, $iv);
	$expires = Util::getLongExpirationDate();
	//Create the ready and store in DB
	$document = array(
		"id"=>$key,
		"content"=>bin2hex($encrypted),
		"expires"=>$expires
		);
	$con->insert("ready", $document);
	$toNotify = $entry->trustee;
	$mailer = new Mailer();
	$link = "reclaim.php?i=$key&code=$iv";
	//Notify the user that the claimable is ready by sending a link
	$mailer->notifyClaimReady($toNotify, $link);
	//Delete from pending db
	$toDelete = $entry->_id;
	$con->delete("pending", array("_id"=>$toDelete));
}

$con = new Congo();
$cooldown = strtotime('now');
$q = $con->find("pending", array("cooldown"=>array('$lt'=>$cooldown)));

if($q->hasResults()) {
	//Send notifications and delete
	$entries = $q->getResults();
	foreach($entries as $entry) {
		processAndDelete($entry, $con);
	}
}
?>