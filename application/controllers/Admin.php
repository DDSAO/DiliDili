<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

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
	public function index()
	{
		$this->load->model('model');
		print('users');
		print("<pre>".print_r($this->model->getUsers(),true)."</pre>");
		print('notification');
		print("<pre>".print_r($this->model->getNotifications(),true)."</pre>");
		print('user auth');
		print("<pre>".print_r($this->model->getUserAuths(),true)."</pre>");
		print('indexes');
		print("<pre>".print_r($this->model->getIndexes(),true)."</pre>");
		print('videos');
		print("<pre>".print_r($this->model->getVideos(),true)."</pre>");
		print('danmu');
		print("<pre>".print_r($this->model->getDanmus(),true)."</pre>");
		print('Latest');
		print("<pre>".print_r($this->model->getLatestVideos(),true)."</pre>");
        $this->load->view('header');
        $this->load->view('admin');
        $this->load->view('footer');
    }
    public function destroy(){
        $this->load->model('model');
        $this->model->resetDatabase();
	}
	public function phpinfo(){
		$this->load->view('phpinfo');
	}
	public function implemented(){
		$this->load->view('implemented');
	}
	public function testGet(){
		$message = '';
		for($i=0;$i<1000;$i++){
			$message .= '<p>'.$i.'</p>';
		}
		$data['message'] = $message;
		$this->load->view('notice',$data);
	}
	public function send(){
		//getting token
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
		curl_setopt($ch, CURLOPT_URL,"https://tapi.telstra.com/v2/oauth/token");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
					"grant_type=client_credentials&client_id=ap5dnnDRDHfUZliW5EwVfC42DCpdKmnR&client_secret=rn4CJ9gjxlyShF1Z&scope=NSMS");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		$outJson = json_decode($server_output,true);
		//using token to send sms
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer ".$outJson["access_token"],
			"Content-Type: application/json",
		));
		curl_setopt($ch2, CURLOPT_URL,"https://tapi.telstra.com/v2/messages/sms");
		curl_setopt($ch2, CURLOPT_POST, true);
		$data = array(
			"to"=>"0432092214",
			"body"=>"Hello DDSAO",
		);
		$dataJson = json_encode($data);
		curl_setopt($ch2, CURLOPT_POSTFIELDS,$dataJson);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch2);
		curl_close ($ch2);
		echo json_decode($server_output,true);
	}
}
