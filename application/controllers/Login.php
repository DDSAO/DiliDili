<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index()
	{
        $this->load->helper(array('form'));

        $this->load->library('form_validation');

        $this->load->model('model');
        
        $data['redirect'] = $this->input->get('redirect');

        $config = array(
            array(
                'field' => 'name',
                'label' => 'User Name',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|trim',
            ),
        );
        $this->form_validation->set_rules($config);

        $data['message'] = "";
    

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('header');
            $this->load->view('login',$data);
            $this->load->view('footer');
        }
        else
        {
            $name = $this->input->post('name');
            $password = $this->input->post('password');
            $verifyResult = $this->model->verifyUser($name, $password);
            if($verifyResult['login'] === 1 && $verifyResult['activated'] === 1){

                if ($this->input->post('remember')!== null){
                    set_cookie('id',$verifyResult['id'],3600);
                    set_cookie('name',$verifyResult['name'],3600);
                    set_cookie('password',$password,3600);
                    set_cookie('login',1,3600);
                    $this->session->set_userdata(array(
                        'transferCookie'=>true
                    ));
                } else {
                    delete_cookie('id');
                    delete_cookie('name');
                    delete_cookie('password');
                    delete_cookie('login');
                }
                
                $this->session->set_userdata(array(
                    'userInfo'=>$verifyResult
                ));
                
                if ( $this->input->get('redirect') !== null ) {
                    
                    redirect($this->input->get('redirect'));
                } else{
                    redirect('homepage','refresh');
                }
                
            
            } else if ($verifyResult['login'] === 1 && $verifyResult['activated'] === 0) {
                $data['message'] = 'You havent verify your account yet, please check your email or '.
                    '<a href="'.base_url('signup/resend/'.$verifyResult['id']).'">Resend verification email</a>';
                $this->load->view('header');
                $this->load->view('notice',$data);
                $this->load->view('footer');

            } else {
                $data['message'] = $verifyResult['message'];
                $this->load->view('header');
                $this->load->view('login',$data);
                $this->load->view('footer');
            }
            
        }

        }
        public function forgetPassword(){
            $data['message'] = 
                '<p class="m-2">Please input your user name here to verify</p>
                <form class="container" method="post" action="'.base_url('login/changePassword').'">
                
                    <label>User Name:</label><input class="" type="text" name="name" placeholder="User Name">
                    <label>Email:</label><input class="" type="text" name="email" placeholder="Email">
                    <label>Phone:</label><input class="" type="text" name="phone" placeholder="Phone Number">
                    <button type="submit" class="btn btn-primary">
                    submit
                    </button>
          
                </form>
                ';
            $this->load->view('header');
            $this->load->view('notice',$data);
            $this->load->view('footer');
        }
        public function changePassword(){
            $this->load->model('model');

            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $user = $this->model->searchUser($name);
            if ($user !== null) {
                if ($user['email']===$email && $user['phone']===$phone) {
                    $data['id'] = $user['id'];
                    $data['email'] = $email;
                    $data['phone'] = $phone;
                    $this->load->view('header');
                    $this->load->view('dashboard/changePassword',$data);
                    $this->load->view('footer');
                    return;
                } else {
                    $data['message'] = 
                    "<p>The name does not exist, check again or register an new account</p>
                    <a href='login/forgetPassword'>Forget Password</a>
                    ";
                }
            } else {
                $data['message'] = 
                "<p>The info does not match! Check again or register an new account</p>
                <a href='login/forgetPassword'>Forget Password</a>
                ";
            }
            $this->load->view('header');
            $this->load->view('notice',$data);
            $this->load->view('footer');

            
        }

}


