<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	public function index($category)
	{
        $category = strtolower($category);
        $this->load->model('model');
        if (get_cookie("loaded-".$category) != null){
            $data['videos'] = $this->model->getVideosByCategory($category,0,intval(get_cookie("loaded-".$category)));
        } else {
            $data['videos'] = $this->model->getVideosByCategory($category,0,5);
        }
        
        $data['loaded'] = count($data['videos']);
        $data['category'] = $category;

        $userInfo = $this->session->userdata('userInfo');
        $id = isset($userInfo) ? $userInfo['id'] : get_cookie('id');
        $login = isset($userInfo) ? $userInfo['login'] : get_cookie('login');


        if ($login === 1){
            $data['login'] = 1;
            $data['id'] = $this->session->userdata('userInfo')['id'];
            $data['hasNew'] = $this->model->hasNewNotification($id);
        } else {
            $data['login'] = 0;
        }

        $this->load->view('header',$data);
        $this->load->view('navbar',$data);
        $this->load->view('category',$data);
        $this->load->view('footer');
    }
    public function getMore(){
        $this->load->model('model');
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = (array) json_decode($stream_clean);
        echo json_encode($this->model->getVideosByCategory($request['category'],$request['loaded'],0,5)) ;
    }
}

