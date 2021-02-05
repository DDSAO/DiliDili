<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($videoNumber)
	{
		$this->load->model('model');
		$this->model->addView($videoNumber);
		$video = $this->model->getVideo($videoNumber);
		if ($video['success'] !== 1){
			$data['message'] = "the video not found!";
			$this->load->view('header');
			$this->load->view('notice',$data);
			$this->load->view('footer');
			
		} else {
			
			$data = $video;
			$data['vid'] = $video['id'];
			$uploaderInfo = $this->model->getUserInfo($video['uploader']);
			
			//get video's resolution
			$command = 'ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 ' . 
				$_SERVER['DOCUMENT_ROOT'].'/'. $video['videoLocation'].' 2>&1' ;
			$resolution = explode('x',shell_exec($command));
			$data['vwidth'] = $resolution[0];
			$data['vheight'] = $resolution[1];
			//1.778 is the default ratio, > means too wide, < means too high
			if( floatval($resolution[0])/floatval($resolution[1]) > 1.778) {
				$data['style'] = 'style="width:100%"';
			} else {
				$data['style'] = 'style="height:100%"';
			}


			$data['fans'] = count($uploaderInfo['fans']);
			$data['udescription'] = $uploaderInfo['description'];
			$userInfo = $this->session->userdata('userInfo');
			$id = isset($userInfo) ? $userInfo['id'] : get_cookie('id');
			$login = isset($userInfo) ? $userInfo['login'] : get_cookie('login');
			if ($login === 1){
				$data['login'] = 1;
				$data['id'] = $id;
				$data['uid'] = $id;
				$data['subscribed'] = $this->model->isSubscribe($video['uploader'],$data['uid']);
				$data['liked'] = $this->model->isLiked($video['id'],$data['uid']);
				$data['hasNew'] = $this->model->hasNewNotification($id);
			} else {
				$data['login'] = 0;
				$data['id']=-1;
				$data['uid'] = -1;
				$data['subscribed'] = null;
				$data['liked'] = null;
			} 
			$danmu =$this->model->getDanmu($videoNumber);
			function cmp($a,$b){
				return $a['time'] - $b['time'];
			}
			usort($danmu, "cmp");
			$data['danmu'] = $danmu;

			
			
			$this->load->view('header',$data);
			$this->load->view('navbar',$data);
			$this->load->view('video',$data);
			$this->load->view('footer');
			
		}
	}
	public function submitDanmu(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$danmuInfo = (array) json_decode($stream_clean);
			$this->model->addDanmu($danmuInfo);
			echo json_encode(array('success'=>1));
		} 
	}
	public function submitComment(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$commentInfo = (array) json_decode($stream_clean);
			$this->model->addComment($commentInfo);
			echo json_encode(array('success'=>1));
		} 
	}


	public function subscribe(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$info = (array) json_decode($stream_clean);
			if(! $this->model->isSubscribe($info['uploader'],$info['uid'])){
				$this->model->subscribe($info);
				echo json_encode(array('success'=>1));
			} else {
				echo json_encode(array('success'=>0,'message'=>'already subscribed?'));
			}
		} 
	}
	public function unsubscribe(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$info = (array) json_decode($stream_clean);
			if($this->model->isSubscribe($info['uploader'],$info['uid'])){
				$this->model->unsubscribe($info);
				echo json_encode(array('success'=>1));
			} else {
				echo json_encode(array('success'=>0,'message'=>'was not subscribed?','result'=>$this->model->isSubscribe($info['uploader'],$info['uid'])));
			}
		} 
	}
	public function like(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$info = (array) json_decode($stream_clean);
			if(! $this->model->isLiked($info['vid'],$info['uid'])){
				$this->model->like($info);
				echo json_encode(array('success'=>1));
			} else {
				echo json_encode(array('success'=>0,'message'=>'already liked?'));
			}
		} 
	}
	public function unLike(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->model('model');
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$info = (array) json_decode($stream_clean);
			if($this->model->isLiked($info['vid'],$info['uid'])){
				$this->model->unLike($info);
				echo json_encode(array('success'=>1));
			} else {
				echo json_encode(array('success'=>0,'message'=>'already liked?'));
			}
		} 
	}
	public function donate(){
		
	}

}
