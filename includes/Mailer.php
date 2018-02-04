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

	public function notifySuccessfulStorageWithTrustees($to, $name, $trustees, $witnesses) {
		//TODO: implement
		//$f = new StoredFormater($name);
		//$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyTrustees($trustees, $user, $name, $key) {
		//TODO: Let know the trustees that there is a doc available and accessible with the following key
	}

	public function notifyWitness($witnes, $user, $name, $iv) {
		//TODOL Let the witness know that he has been selected as a witness
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

	public function notifyClaimReady($to, $link) {
		$f = new ClaimReadyFormater($link);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyTrusteeAboutDenial($to) {
		$f = new NotifyClaimDenialFormater();
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The claim $code has been denied by the owner
	}

	public function notifyTrusteeAboutPending($to) {
		$f = new NotifySuccessfulDenialFormater();
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The resource is pending to be claimed you will be notified once the resource is ready
	}

	public function notifyOwnerAboutClaim($to, $name, $code, $link, $trustee) {
		$f = new notifyOwnerAboutClaimFormater($name, $code, $link, $trustee);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The resource has been claimed by $trustee, you can deny this claim by following this link
	}

	public function notifyOwnerAboutDenial($to, $code) {
		$f = new NotifySuccessfulDenialFormater($code);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//Claim with code $code has been denied properly
	}

	//$mailer->notifyOwnerAboutClaim($user, $name, $trustee);
	//$mailer->notifyTrusteeAboutPending($trustee, $name);

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