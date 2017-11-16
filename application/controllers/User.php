<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('Model');
	}

    public function index_get(){
    	$output = $this->db->get("user")->result();
    	$this->set_response($output, 200);
  	}

  	function index_post() {
        $username = $this->input->post('username'); $email = $this->input->post('email');
        $checkUsr = $this->db->where(array('oauth_provider'=>'email', 'username'=>$username))->count_all_results('user');
        $checkEm = $this->db->where(array('oauth_provider'=>'email', 'email'=>$email))->count_all_results('user');
        if($checkUsr == 0){
            if($checkEm == 0){
                $data = array(
                    'uid'               => $this->Model->generateUID(),
                    'oauth_provider'    => 'email',
                    'dspname'           => $this->post('dspname'),
                    'username'          => $username,
                    'email'             => $email,
                    'password'          => $this->post('password'),
                    'status'            => 'unverified',
                    'created'           => date("Y-m-d H:i:s")
                );
                if($this->db->insert('user', $data)) {
                    $response = array('code'=>1, 'message'=>'Registration success');
                }else{
                    $response = array('code'=>2, 'message'=>'Registration failed');
                }
            }else{
                $response = array('code'=>3, 'message'=>'Email already registered');
            }
        }else{
            $response = array('code'=> 4, 'message'=>'Username already exist');
        }
        $this->response($response, 200);
    }

    public function login(){
        $username = $this->input->post('username');
        $pass = $this->input->post('password');
    }
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */