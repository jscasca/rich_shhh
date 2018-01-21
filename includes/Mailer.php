<?php

require('Mailin.php');
require('Formater.php');

class Mailer {
	private $apiKey = '<api>';
	private $service = 'https://api.sendinblue.com/v2.0';
	private $mailer;

	public function __construct() {
		$this->mailer = new Mailin($this->service, $this->apiKey);
	}

	public function notifySuccessfulStorage($to, $name) {
		$f = new StoredFormater($name);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyFailedStorage($to, $name) {
		$f = new DuplicateFormater($name);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyMissingRetrieval($to, $name) {
		//Someone tried to access a resource on your user that does not exist
		$f = new MissingFormater($name);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyResourceReady($to, $name, $link) {
		//
		$f = new ReadyFormater($name, $link);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function sendMail($to, $subject, $plainContent, $htmlContent) {
		//
		$data = array(
			"to" => array($to => "Dear user"),
			/*"cc" => array(), //Not needed */
			/*"bcc" => array(), //Not needed */
			"from" => array("support@prologes.com"), //TODO: change to the actual email
			/*"replyto" => array(), //Not needed */
			"subject" => $subject,
			"text" => $plainContent,
			"html" => $htmlContent,
			/*"attachment" => array(), //Attachments not needed*/
			"header" => array("Content-Type"=> "text/html; charset=iso-8859-1"),
			/*"inline_image" => array() Not needed*/);
		return $this->mailer->send_email($data);
	}
}


?>