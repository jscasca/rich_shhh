<?php
require('../includes/Congo.php');
require('../includes/Mailer.php');

$con = new Congo();
$cooldown = strtotime('now');
$q = $con->find("pending", array("cooldown"=>array('$lt'=>$cooldown)));

if($q->hasResults()) {
	//Send notifications and delete
	$entries = $q->getResults();
	foreach($entries as $entry) {
		//Email and delete
		// Each entry = { _id, id, $denyCode, $trusteeEmail, $content}

		// New Doc
	}
}
?>