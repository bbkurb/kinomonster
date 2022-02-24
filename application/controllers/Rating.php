<?php 

defined('BASEPATH') OR exit('No direct script access allowed!');

class Rating extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('rating_model');
	}
#---------------------------------------------------------
	public function index($slug = NULL) {
		$this->data['movie_data'] = null;
		/*-----------PAGINATION----------*/
		$this->load->library('pagination'); //import pagination
		$offset = (int) $this->uri->segment(4);
		$row_count = 4;
		
		$count = count($this->rating_model->getRatings(0, 1));
		$p_config['base_url'] = '/rating/';
		$this->data['title'] = "KinoMonster - Rating";
		$this->data['movie_data'] = $this->rating_model->getMoviesOnPage($row_count, $offset, $type = 1);
		

		/*if($this->data['movie_data'] == null) {
			show_404();
		}
*/
		/*-----PAGINATION CONFIG*/
		$p_config['total_rows'] = $count;
		$p_config['per_page'] = $row_count;

		/*-----PAGINATION BOOTSTRAP CODE-----*/
		$p_config['full_tag_open'] = "<ul class='pagination'>";
		$p_config['full_tag_close'] ="</ul>";
		$p_config['num_tag_open'] = '<li>';
		$p_config['num_tag_close'] = '</li>';
		$p_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$p_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$p_config['next_tag_open'] = "<li>";
		$p_config['next_tagl_close'] = "</li>";
		$p_config['prev_tag_open'] = "<li>";
		$p_config['prev_tagl_close'] = "</li>";
		$p_config['first_tag_open'] = "<li>";
		$p_config['first_tagl_close'] = "</li>";
		$p_config['last_tag_open'] = "<li>";
		$p_config['last_tagl_close'] = "</li>";

		/*-----инициализация пагинации*/
		$this->pagination->initialize($p_config);
		$this->data['pagination'] = $this->pagination->create_links();
		
		$this->data['rating'] = $this->rating_model->getRatings();

		$this->load->view('templates/header', $this->data);
		$this->load->view('rating/index', $this->data);
		$this->load->view('templates/footer');
	}



}