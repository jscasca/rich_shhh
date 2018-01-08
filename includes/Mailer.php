<?php

if(!isset($server) || !$server) {
	header("HTTP/1.1 404 Not Found");
	die();
}

require('Mailin.php');
require('Formater.php');

class Mailer {
	private $apiKey = '<api key>';
	private $service = 'https://api.sendinblue.com/v2.0';
	private $mailer;

	public function __construct() {
		$this->mailer = new Mailin($this->service, $this->apiKey);
	}

	public function notifyFailedStorage($to, $name) {
		//
		$subject = Formater::duplicateResourceSubject();
		$plainContent = Formater::duplicateResourcePlainContent($name);
		$htmlContent = Formater::duplicateResourceHtmlContent($name);
		$this->sendMail($to, $subject, $plainContent, $htmlContent);
	}

	public function sendMail($to, $subject, $plainContent, $htmlContent) {
		//
		$data = array(
			"to" => array($to => "Dear user"),
			/*"cc" => array(), //Not needed */
			/*"bcc" => array(), //Not needed */
			"from" => array("support@prologes"), //TODO: change to the actual email
			/*"replyto" => array(), //Not needed */
			"subject" => $subject,
			"text" => $plainContent,
			"html" => $htmlContent,
			/*"attachment" => array(), //Attachments not needed*/
			"header" => array("Content-Type"=> "text/html; charset=iso-8859-1"),
			/*"inline_image" => array() Not needed*/);
		$this->mailer->send_email($data);
	}
}


?>