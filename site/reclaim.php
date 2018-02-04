<?php

if(!isset($_REQUEST['i']) || !isset($_REQUEST['code'])) {
	header("HTTP/1.1 404 Not Found");
	die();
}
session_start();

$_SESSION['i'] = $_REQUEST['i'];
$_SESSION['code'] = $_REQUEST['code'];

header("Location: http://localhost:3000/claim?i=".$_REQUEST['i']."&code=".$_REQUEST['code']);

?>