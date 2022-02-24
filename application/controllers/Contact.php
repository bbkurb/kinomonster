<?php 

defined('BASEPATH') OR exit('No direct script access allowed!');

class Contact extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('contact_model');
	}
#---------------------------------------------------------
	public function index() {
		$this->load->library('email');

		$this->data['title'] = "KinoMonster - Contacts";

		$this->email->from('bbkurb@example.com', 'Your Name');
		$this->email->to('someone@example.com');
		$this->email->cc('another@another-example.com');
		$this->email->bcc('them@their-example.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();
	}

}

