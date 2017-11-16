<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Card extends REST_Controller {

	public function __construct(){
		parent::__construct();
	}

    public function index_get(){
    	$uid = $this->input->get('uid');
    	$output = $this->db->where('uid', $uid)->get('card')->result();
    	$this->set_response($output, 200);
  	}

  	function index_post() {

    }
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */