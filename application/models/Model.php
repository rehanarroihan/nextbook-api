<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {

	public function generateUID(){
        $query = $this->db->order_by('uid', 'DESC')->limit(1)->get('user')->row('uid');
        $lastNo = substr($query, 3, 3);
        $next = $lastNo + 1;
        $kd = 'usr';
        return $kd.sprintf('%03s', $next).date('ymd');
    }

}

/* End of file Model.php */
/* Location: ./application/models/Model.php */