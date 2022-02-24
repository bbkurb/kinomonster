<?php 
class Contact_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function getContact($username, $user_email, $feedback) {
		$data = array (
			'username' => $username,
			'user_email' => $user_email,
			'feedback' => $feedback,
		);

		return $this->db->insert('feedback', $data);
	}


}