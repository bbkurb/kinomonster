<?php 
class Rating_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function getRatings($slug = FALSE) {
		if($slug === FALSE) {
			$query = $this->db
			->order_by('rating', 'desc')
			->where('rating>', 0)
			->get('movie');
		return $query->result_array();
		}

		$query = $this->db->get_where('movie', array('slug' => $slug));
		return $query->row_array();
	}

/*--------------ПАГИНАЦИЯ-----------------------*/
	public function getMoviesOnPage($row_count, $offset, $type = 1) {
		$query = $this->db
			->where('category_id', $type)
			->order_by('add_date', 'desc')
			->get('movie', $row_count, $offset);

		return $query->result_array();
	}

}