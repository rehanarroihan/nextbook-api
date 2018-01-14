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

    public function getClassID(){
		return $this->db->where('uid', $this->session->userdata('uid'))
								->get('user')->row()->classid;
	}

    public function getDayList($cid, $day){
		return $this->db->join('lesson', 'lesson.lessonid = schedule.lessonid', 'left')
		 					->where('schedule.classid', $cid)
							->where('schedule.day', $day)->order_by('schedule.start', 'asc')
							->get('schedule')->result();
	}

	public function getLessonList($cid){
		return $this->db->where('classid', $cid)->get('lesson')->result();
	}

	public function getDayCount($cid, $day){
		return $this->db->where('classid', $cid)->where('day', $day)->count_all_results('schedule');
	}

	public function getLessonNow($classid)
	{
		$timenow = date('H:i');
		$daynow = date('l');
		$day = null;
		if ($daynow == 'Sunday') {
			$day = 'minggu';
		} elseif ($daynow == 'Monday') {
			$day = 'senin';
		} elseif ($daynow == 'Tuesday') {
			$day = 'selasa';
		} elseif ($daynow == 'Wednesday') {
			$day = 'rabu';
		} elseif ($daynow == 'Thursday') {
			$day = 'kamis';
		} elseif ($daynow == 'Friday') {
			$day = 'jumat';
		} elseif ($daynow == 'Saturday') {
			$day = 'sabtu';
		}
		return $this->db->where('schedule.classid',$classid)
				 		->where('schedule.day',$day)
						->where('schedule.start <=',$timenow)
				 		->where('schedule.end >',$timenow)
				 		->join('lesson', 'lesson.lessonid = schedule.lessonid')
				 		->get('schedule')
				 		->row();
	}

	public function getNextLesson($classid){
		$timenow = date('H:i');
		$daynow = date('l');
		$day = null;
		if ($daynow == 'Sunday') {
			$day = 'minggu';
		} elseif ($daynow == 'Monday') {
			$day = 'senin';
		} elseif ($daynow == 'Tuesday') {
			$day = 'selasa';
		} elseif ($daynow == 'Wednesday') {
			$day = 'rabu';
		} elseif ($daynow == 'Thursday') {
			$day = 'kamis';
		} elseif ($daynow == 'Friday') {
			$day = 'jumat';
		} elseif ($daynow == 'Saturday') {
			$day = 'sabtu';
		}
		return $this->db->where('schedule.classid',$classid)
						->where('schedule.day',$day)
						->where('schedule.start >=',$timenow)
				 		->join('lesson', 'lesson.lessonid = schedule.lessonid')
				 		->get('schedule')
				 		->row();
	}

	public function generateCode(){
		$done = 0;
		do{
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	    	$code = '';
	    	for ($i = 0; $i < 7; $i++){
	        	$code .= $characters[mt_rand(0, 61)];
	    	}
	    	$check = $this->db->where('classid', $code)->get('class');
	    	if($check->num_rows() == 0){
	    		$done = 1;
	    	}else{
	    		$code = '';
	    	}
		}while($done != 1);
		return $code;
	}

}

/* End of file Model.php */
/* Location: ./application/models/Model.php */