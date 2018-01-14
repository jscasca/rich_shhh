<?php
//validate the link
if(!isset($_REQUEST['i']) || !isset($_REQUEST['code'])) {
	header("HTTP/1.1 404 Not Found");
	die();
}
session_start();

if(!isset($_SESSION['counter'])) {
	$_SESSION['counter'] = 0;
} else {
	$_SESSION['counter'] = 1;
}

$_SESSION['i'] = $_REQUEST['i'];
$_SESSION['code'] = $_REQUEST['code'];
?>
<html>
	<head>
		<!-- head -->
		<script src="./js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="./css/bootstrap.min.css">
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script> -->
		<style>
		.label-input, input.label-input:focus {
			background: none repeat scroll 0 0 transparent;
			border: medium none !important;
			color: #000000;
			outline: none;
		}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="form">
			<form action="content.php" method="post" id="form">
				<!-- form -->
				<div class="user" id="user">
					<!-- user -->
					<div class="form-group">
						<label for="user">Lucky</label>
						<input type="text" class="form-control" id="lucky" name="lucky" placeholder="Your lucky number">
					</div>
					<div class="form-group">
						<label for="name">Secret</label>
						<input type="password" class="form-control" id="secret" name="secret" placeholder="Your secret">
					</div>
				</div>
				<div class="submit">
					<div class="button-group">
						<button type="submit" id="submit">Submit</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</body>
</html>
<script>


function validateContent(e) {
	//e.preventDefault();
}

function load() {

	var form = document.getElementById("form");
	form.addEventListener("submit", function(e) { validateContent(e);}, false)
}

document.addEventListener("DOMContentLoaded", load, false);
	//
</script>