<?php

$server = true;

require('../includes/Mailer.php');

$mailer = new Mailer();
var_dump($mailer->sendMail("jscasca@gmail.com", "Testing SB", "Simple text", "HTML text"));

?>