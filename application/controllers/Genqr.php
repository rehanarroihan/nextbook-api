<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Endroid\QrCode\QrCode;
require APPPATH .'libraries/vendor/autoload.php';

class Genqr extends CI_Controller {

	public function index(){
		header('Content-Type: image/png');
        $qr = new QrCode($this->input->get('text')); 
        $qr->setSize($this->input->get('size'));
        echo $qr->writeString();
	}
}

/* End of file Genqr.php */
/* Location: ./application/controllers/Genqr.php */