<?php

header('Access-Control-Allow-Origin: http://localhost:3000', false);
header('Access-Control-Allow-Methods: POST,GET', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
//validate the link

if(!isset($_REQUEST['i']) || !isset($_REQUEST['code'])) {
	header("HTTP/1.1 404 Not Found");
	die();
}
session_start();

$_SESSION['i'] = $_REQUEST['i'];
$_SESSION['code'] = $_REQUEST['code'];

header("Location: http://localhost:3000/recover?i=".$_REQUEST['i']."&code=".$_REQUEST['code']);

?>