<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Aclass extends REST_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('Model');
	}

    public function index_get(){}

  	public function index_post(){
        $uid = $this->input->post('uid');
        $code = $this->Model->generateCode();
        $object = array(
            'classid'       => $code, 
            'created_by'    => $uid,
            'name'          => $this->input->post('name'),
            'descript'      => $this->input->post('descript'),
            'dt_created'    => date("Y-m-d"),
            'photo'         => 'group.png'
        );
        $this->db->insert('class', $object);
        $this->db->where('uid', $uid)->update('user',  array('classid' => $code));
        if($this->db->affected_rows() > 0){
            $output = array('code' => 1, 'message'=>'Berhasil membuat kelas');
        }else{
            $output = array('code' => 2, 'message'=>'Gagal membuat kelas');
        }
        $this->set_response($output, 200);
    }

    public function ishave_get(){
    	$id = $this->input->get('id');
    	$query = $this->db->where('uid', $id)->get('user')->row()->classid;
    	if($query != NULL){
    		$output = array(
    			'code' => 1,
    			'classid' => $query,
    			'message' => 'Has joined class'  
    		);
    	}else{
    		$output = array(
    			'code' => 2,
    			'message' => 'Not join any class'
    		);
    	}
    	$this->set_response($output, 200);
    }

    public function getmember_get(){
    	$classid = $this->input->get('cid');
        $query = $this->db->where('classid', $classid)->get('user')->result();
        $output = array();
        $i = 0;
        foreach ($query as $key) {
            $output [$i]['name'] = $key->dspname;
            $output [$i]['pp'] = $key->profilepict;
            $output [$i]['email'] = $key->email;
            $output [$i]['prov'] = $key->oauth_provider;
            $output [$i]['pps'] = $key->picture_url; 
            $i++;
        }
        $this->set_response($output, 200);
    }

    public function classinfo_get(){
    	$classid = $this->input->get('cid');
    	$query = $this->db->where('classid', $classid)->get('class')->row();
        $membercount = $this->db->where('classid', $classid)->get('user')->num_rows();
        $join = $this->db->where('uid', $query->created_by)
                    ->join('class', 'class.created_by = user.uid', 'left')->get('user')->row();
        $output = array(
            'classid' => $query->classid,
            'created_by' => $join->dspname,
            'class_name' => $query->name,
            'class_descr' => $query->descript,
            'groupimg' => $query->photo,
            'member' => $membercount
        );
    	$this->set_response($output, 200);
    }

    public function checkcode_get(){
        $code = $this->input->get('code');
        $uid = $this->input->get('uid');
        $check = $this->db->where('classid', $code)->get('class');
        if($check->num_rows() > 0){
            $this->db->where('uid', $uid)->update('user',  array('classid' => $code));
            if($this->db->affected_rows() > 0){
                $output = array(
                    'code' => 1,
                    'message' => 'Class found' 
                );
            }else{
                $output = array(
                    'code' => 3,
                    'message' => 'An error occurred' 
                );
            }
        }else{
            $output = array(
                'code' => 2,
                'message' => 'Class not found' 
            );
        }
        $this->set_response($output, 200);
    }

    public function schedulecount_get(){
        $cid = $this->input->get('cid');
        $day = $this->input->get('day');
        if($this->Model->getDayCount($cid, $day) == 0){
            $output = array(
                'code' => 1,
                'message' => 'Tidak ada pelajaran' 
            );
        }else{
            $output = array(
                'code' => 2,
                'message' => 'Ada pelajaran' 
            );
        }
        $this->set_response($output, 200);
    }

    public function schedule_get(){
        if($this->input->get('day')){
            $cid = $this->input->get('cid');
            $output = $this->Model->getDayList($cid, $this->input->get('day'));
            $this->set_response($output, 200);
        }else{

        }
    }

    public function unenroll_post(){
        if($this->input->post('uid')){
            $this->db->where('uid', $this->input->post('uid'))->update('user',  array('classid' => NULL));
            if($this->db->affected_rows() > 0){
                $output = array(
                    'code' => 1,
                    'message' => 'Berhasil keluar kelas' 
                );
            }else{
                $output = array(
                    'code' => 2,
                    'message' => 'Gagal keluar kelas' 
                );
            }
            $this->set_response($output, 200);
        }
    }

    public function lessonnow_get()
    {
        $classid = $this->input->get('cid');
        $doe = $this->Model->getLessonNow($classid);
        if(count($doe) > 0){
            $output['code'] = 1;
            $output['lesson'] = $doe->lesson;
            $output['lessonid'] = $doe->lessonid;
        }else{
            $output['code'] = 2;
            $output['lesson'] = 'Tidak Ada';
            $output['lessonid'] = 0;
        }

        $deo = $this->Model->getNextLesson($classid);
        if (count($deo) > 0) {
                $output['nextlesson'] = $deo->lesson;
                $output['nextlessonTime'] = $deo->start;
            }else{
                $output['nextlesson'] = 'Tidak Ada';
                $output['nextlessonTime'] = "";
            }
        $this->set_response($output, 200);
    }

    public function postlist_post(){
        // Image Upload
        $configpict = array();
        $configpict['upload_path'] = './assets/2.0/file/img/';
        $configpict['allowed_types'] = 'gif|jpg|png';
        $configpict['max_size'] = '100000';
        $this->load->library('upload',$configpict, 'imageupload');
        $this->imageupload->initialize($configpict);
        $uploadimage = $this->imageupload->do_upload('postpict');

        // Document Upload
        $configdoc = array();
        $configdoc['upload_path'] = './assets/2.0/file/doc/';
        $configdoc['allowed_types'] = 'txt|doc|docx|ppt|pptx|xls|xlsx|pdf|zip|rar';
        $configdoc['max_size'] = '300000';
        $this->load->library('upload', $configdoc, 'documentupload');
        $this->documentupload->initialize($configdoc);
        $uploaddocument = $this->documentupload->do_upload('postfile');

        if ($this->input->post('content') != NULL) {
            if ($uploadimage) {
                if ($uploaddocument) {
                    $imagedata = $this->imageupload->data();
                    $documentdata = $this->documentupload->data();
                    if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                        $this->session->set_flashdata('announce', 'Success to Upload');
                        redirect('aclass/home');
                    }
                }else{
                    $documentdata = 'NULL';
                    $imagedata = $this->imageupload->data();
                    if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                        $this->session->set_flashdata('announce', 'Success to Upload');
                        redirect('aclass/home');
                    }
                }
            }elseif($uploaddocument){
                $imagedata = 'NULL';
                $documentdata = $this->documentupload->data();
                if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                    $this->session->set_flashdata('announce', 'Success to Upload');
                    redirect('aclass/home');
                }
            }else{
                $imagedata = 'NULL';
                $documentdata = 'NULL';
                if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                    $this->session->set_flashdata('announce', 'Success to Upload');
                    redirect('aclass/home');
                }
            }
        } elseif($uploadimage) {
            if ($uploaddocument) {
                $imagedata = $this->imageupload->data();
                $documentdata = $this->documentupload->data();
                if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                    $this->session->set_flashdata('announce', 'Success to Upload');
                    redirect('aclass/home');
                }
            }else{
                $documentdata = 'NULL';
                $imagedata = $this->imageupload->data();
                if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                    $this->session->set_flashdata('announce', 'Success to Upload');
                    redirect('aclass/home');
                }
            }
        }elseif($uploaddocument){
            $imagedata = 'NULL';
            $documentdata = $this->documentupload->data();
            if ($this->Class_model->posting($imagedata,$documentdata) == TRUE) {
                $this->session->set_flashdata('announce', 'Success to Upload');
                redirect('aclass/home');
            }
        }else{
            $this->session->set_flashdata('announce', 'Please Input Something');
            redirect('aclass/home');
        }
    }

    public function postlist_get()
    {
        $classid = $this->input->get('cid');
        $lesson = $this->Model->getLessonList($classid);
        // if (count($lesson) > 0) {
        //     $query = $this->db->where('userpost.classid',$classid)
        //     ->order_by('userpost.creat', 'DESC')
        //                     ->join('user','user.uid = userpost.userid')
        //                     ->join('lesson','lesson.lessonid = userpost.lessonid', 'left')
        //                     ->get('userpost')
        //                     ->result();
        // } else {
        //     $query = $this->db->where('userpost.classid',$classid)
        //     ->order_by('userpost.creat', 'DESC')
        //                     ->join('user','user.uid = userpost.userid', 'left')
        //                     ->get('userpost')
        //                     ->result();
        // }

        $query = $this->db->where('userpost.classid',$classid)
            ->order_by('userpost.creat', 'DESC')
                            ->join('user','user.uid = userpost.userid')
                            ->join('lesson','lesson.lessonid = userpost.lessonid', 'left')
                            ->get('userpost')
                            ->result();

        $output = array();
        $i = 0;
        foreach ($query as $key) {
            $queryk = $this->db->where('postid', $key->postid)->count_all_results('comment');
            $output [$i]['postid'] = $key->postid;
            $output [$i]['dspname'] = $key->dspname;
            if($key->oauth_provider == 'email'){
                $output [$i]['pict'] = 'http://app.nextbook.cf/assets/2.0/img/user/'.$key->profilepict;
            }else{
                $output [$i]['pict'] = $key->picture_url;
            }
            if($key->lesson == null){
                $output [$i]['lesson'] = "Other";
            }else{
                $output [$i]['lesson'] = $key->lesson;
            }
            $output [$i]['create'] = date('d M Y, H:i',strtotime($key->creat));
            $output [$i]['content'] = $key->content;
            $output [$i]['img'] = $key->img;
            $output [$i]['doc'] = $key->doc;
            $output [$i]['comment'] = $queryk;
            $i++;
        }
        $this->set_response($output, 200);
    }
}