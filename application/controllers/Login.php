<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('Model');
	}

    public function index_get(){
    	$response = array('code'=> 1, 'message'=>'Connected to Server');
        $this->response($response, 200);
  	}

  	function index_post() {
        $username = $this->input->post('username');
        $pass = $this->input->post('password');
        $qr = $this->db->where('username', $username)->where('password', $pass)->get('user');
        if($qr->num_rows() > 0){
            $user = $qr->row();
            if($user->status == 'verified'){
                $response = array(
                    'code'=> 1, 
                    'message'=>'Login success',
                    'fullname'=>$user->dspname,
                    'username'=>$user->username,
                    'email'=>$user->email,
                    'uid'=>$user->uid
                );
                $this->response($response, 200);
            }else{
                $response = array('code'=> 2, 'message'=>'Please check your email for verification');
                $this->response($response, 200);
            }
        }else{
            $response = array('code'=> 3, 'message'=>'Invalid username or password');
            $this->response($response, 200);
        }
    }

    public function useredit_get(){
        $uid = $this->input->get('uid');
        $qry = $this->db->where('uid', $uid)->get('user')->row();
        $output = array(
            'prov' => $qry->oauth_provider,
            'dspname' => $qry->dspname, 
            'username' => $qry->username,
            'email' => $qry->email,
            'picts' => $qry->picture_url,
            'pic' => $qry->profilepict
        );
        $this->response($output, 200);
    }

    public function getuserpic_get(){
        $uid = $this->input->get('uid');
        $query = $this->db->where('uid', $uid)->get('user')->row();
        $output = array(
            'prov' => $query->oauth_provider,
            'picts' => $query->picture_url,
            'pic' => $query->profilepict
        );
        $this->response($output, 200);
    }
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */