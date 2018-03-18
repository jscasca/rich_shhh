<?php

require('Mailin.php');
require('Formater.php');
require_once('Definitions.php');

class Mailer {
	private $apiKey = MAILER_API;
	private $baseUrl = BASE_URL;
	private $sender = MAIL_SENDER;
	private $service = 'https://api.sendinblue.com/v2.0';
	private $mailer;

	public function __construct() {
		$this->mailer = new Mailin($this->service, $this->apiKey);
	}

	private function getLink($link) {
		//validate if needed and attach the link to the base
		return $this->baseUrl . $link;
	}

	public function notifySuccessfulStorage($to, $name) {
		$f = new StoredFormater($name);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifySuccessfulStorageWithTrustees($to, $name, $trustees, $witnesses) {
		$f = new StoredWithExtrasFormater($name, $trustees, $witnesses);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyTrustees($trustees, $user, $name, $key) {
		//TODO: Let know the trustees that there is a doc available and accessible with the following key
		$f = new NotifyTrusteeFormater($name, $user, $key);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyWitness($witnes, $user, $name, $iv) {
		//TODOL Let the witness know that he has been selected as a witness
		$f = new NotifyWitnessFormater($name, $user, $iv);
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
		$f = new ReadyFormater($name, $this->getLink($link));
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyClaimReady($to, $link) {
		$f = new ClaimReadyFormater($this->getLink($link));
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
	}

	public function notifyTrusteeAboutDenial($to) {
		$f = new NotifyClaimDenialFormater();
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The claim $code has been denied by the owner
	}

	public function notifyTrusteeAboutPending($to) {
		$f = new NotifyTrusteeClaimStartedFormater();
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The resource is pending to be claimed you will be notified once the resource is ready
	}

	public function notifyOwnerAboutClaim($to, $name, $code, $link, $trustee) {
		$f = new NotifyOwnerAboutClaimFormater($name, $code, $this->getLink($link), $trustee);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//The resource has been claimed by $trustee, you can deny this claim by following this link
	}

	public function notifyOwnerAboutDenial($to, $code) {
		$f = new NotifySuccessfulDenialFormater($code);
		$this->sendMail($to, $f->subject(), $f->plainContent(), $f->htmlContent());
		//Claim with code $code has been denied properly
	}

	public function sendMail($to, $subject, $plainContent, $htmlContent) {
		//
		$data = array(
			"to" => array($to => "Dear user"),
			/*"cc" => array(), //Not needed */
			/*"bcc" => array(), //Not needed */
			"from" => array($this->sender), //TODO: change to the actual email
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