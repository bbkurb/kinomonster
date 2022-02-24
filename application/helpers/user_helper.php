<?php 

	if(!function_exists('getUserNameById')) {
		function getUserNameById($user_id) {
			$ci=& get_instance();
			$ci->load->model('dx_auth/users');
			$query = $ci->users->get_user_by_id($user_id);
			$result = $query->row();
			return $result;
		}
	}