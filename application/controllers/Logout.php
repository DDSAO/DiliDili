<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

	public function index()
	{
		delete_cookie('login');
		delete_cookie('id');
		delete_cookie('name');
		delete_cookie('password');
        $this->session->sess_destroy();
		redirect('homepage');
	}
}
