<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage extends CI_Controller {

	
	public function index()
	{   
        $this->load->model('model');
        $latestVideos = $this->model->getLatestVideos();
        while (count($latestVideos) < 3){
            array_push($latestVideos,array('title'=>'Empty','coverLocation'=>'img/icon.jpeg','id'=>'not_exist'));
        }
        $data['latestVideos'] = $latestVideos;
        
        $data['all']  = $this->model->getHomepageVideos();
        
        $userInfo = $this->session->userdata('userInfo'); 
        if ($this->session->userdata('transferCookie')) {
            $data['username'] = $userInfo['name'];
        }
        $id = isset($userInfo) ? $userInfo['id'] : get_cookie('id');
        $login = isset($userInfo) ? $userInfo['login'] : get_cookie('login');

        if ($login === 1){
            $data['login'] = 1;
            $data['id'] = $id;
            $data['hasNew'] = $this->model->hasNewNotification($id);
        } else {
            $data['login'] = 0;
        }
        $data['message'] = $this->session->flashdata('message');
        $this->load->view('header',$data);
        $this->load->view('navbar',$data);
        $this->load->view('homepage',$data);
        $this->load->view('footer');
    }
    public function search($word=""){
        $this->load->model('model');
        if ($this->session->userdata('userInfo')['login']===1){
            $data['login'] = 1;
            $data['id'] = $this->session->userdata('userInfo')['id'];
            $data['hasNew'] = $this->model->hasNewNotification($data['id']);
        } else {
            $data['login'] = 0;
        }
        $data['searchWords'] = $word;
        if ($data['searchWords'] != null) {
            $data['result'] = $this->model->searchVideos($data['searchWords']);
        }
        
        $this->load->view('header');
        $this->load->view('navbar',$data);
        $this->load->view('search',$data);
        $this->load->view('footer');
        
    }
    public function searchWord($word=""){
        $this->load->model('model');
        if (! empty($word)) {
            $result = $this->model->autoFill($word);
            if ($result) {
                echo $result[0]['title'];
            } 
        }
        }
        
    
    
}
