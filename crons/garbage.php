<?php

require('../includes/Congo.php');

$con = new Congo();
$expired = strtotime('+1 minutes');
$con->delete("accessible", array("expires"=>array('$lt'=>$expired)));
?>