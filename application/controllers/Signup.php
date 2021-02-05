<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    
	public function index()
	{
        $this->load->model('model');
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $config = array(
            array(
                'field' => 'name',
                'label' => 'User Name',
                'rules' => 'required|trim|callback_checkName',
                'errors' => array(
                    'checkName' => 'The user name has been taken'
                )
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|trim|callback_strength',
                'errors' => array(
                    'strength' => 'The password should have at least 8 characters! Strength not enought!'
                )
            ),
            array(
                'field' => 'confirmPassword',
                'label' => 'Password Confirmation',
                'rules' => 'required|matches[password]|trim'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|trim|callback_checkEmail',
                'errors' => array(
                    'checkEmail' => 'The email has been taken'
                )
            ),
            array(
                'field' => 'phone',
                'label' => 'Phone',
                'rules' => 'required|exact_length[10]|trim',
                'errors' => array(
                    'exact_length[10]' => 'The phone number should be exact 10 digits'
                )),
            array(
                'field' => 'agree',
                'label' => 'Agreement',
                'rules' => 'required',
                'errors' => array(
                    'required' => 'Please agree on our Terms & Conditions'
                )
            )    
        );
        
        $this->form_validation->set_rules($config);
        
       
        $this->collectInfo();
        if ($this->form_validation->run() == FALSE)
        {
            
            $this->load->view('header');
            $this->load->view('signup');
            $this->load->view('footer');
        }
        else
        {
            $userInfo = array(
                'name' => $this->input->post('name'),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
            );
            $result = $this->model->addUser($userInfo);
           
            if ($result['success']===1){
                $data['message'] = 
                    '<p>Activation Email has been send, please verify it before log in.</p>
                    <a href="'.base_url('signup/resend/'.$result['id']).'">Did not recieve?</a>';
                $this->sendActivationEmail($result['id'],$result['email']);
            } else {
                $data['message']="something wrong";
            }
            
            $this->load->view('header');
            $this->load->view('notice',$data);
            $this->load->view('footer');
        }

        }
        
        public function submit(){
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $password = $this->input->post('password');
            $confirmPassword = $this->input->post('confirmPassword');
            $agree = $this->input->post('agree');
            
            $this->load->view('header');
            $this->load->view('notice');
            $this->load->view('footer');
        }
        private function collectInfo(){
            $info = [
                'name' => $this->input->post('name'),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'confirmPassword' => $this->input->post('confirmPassword'),
                'agree' => $this->input->post('agree')
            ];
            $this->session->set_userdata($info);

        }
        public function strength($pwd){
            if (strlen($pwd) < 8) {
                return false;
            }
            return true;
        }

        public function checkNameUnique($name=""){
            if ($name === ""){
                echo 'Empty!';
            } else {
                if ($this->checkName($name)) {
                    echo 'ok';
                } else {
                    echo 'no';
                }
            }
        }
        public function checkName($name) {
            $this->load->model('model');
            if ($this->model->checkNameUnique($name)){
                return true;
            } else {
                return false;
            }
        }
        public function checkEmail($email){
            $this->load->model('model');
            return $this->model->checkEmailUnique($email);
        }
        public function resend($id){
            $this->load->model('model');
            if(! $this->model->isActivated($id)){
                $user = $this->model->getUserInfo($id);
                $this->sendActivationEmail($id,$user['email']);
                $data['message'] = '<div>A new Email has been sent to your email address</div>
                    <a href="'.base_url('signup/resend/'.$id).'">Did not recieve?</a>';
            } else {
                $data['message'] = 'You have already been activated, try Log in!';
            }
            $this->load->view('header');
            $this->load->view('notice',$data);
            $this->load->view('footer');
        }

        private function sendActivationEmail($id,$email){
            $code = $this->addUserAuth($id);
            //sending mail
            $config = [
                'protocol' => 'smtp',
                'smtp_host' => 'mailhub.eait.uq.edu.au',
                'smtp_port' => 25,
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE,
            ];
            $this->load->library('email', $config);       
            $this->email->from('noreply@infs3202-4f9e81fd.uqcloud.net');
            $this->email->to($email);
            $this->email->subject('[DILIDILI]Activate Account');
            $this->email->message('<div>Click this link to activate your account<div><div>'.base_url('signup/activate/'.$id.'/'.$code).'</div>');
            $sent = $this->email->send();
            return $sent;
        }
        private function addUserAuth($id){
            $this->load->model('model');
            $code = "" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
            $this->model->addUserAuth($id, $code);
            return $code;
        }
        public function activate($id, $code){
            
            $this->load->model('model');
            if (! $this->model->isActivated($id)){
                $result = $this->model->activateUser($id, $code);
                if($result){
                    $data['message'] = 'All Done! Try Log in now.';
                    
                } else {
                    $data['message'] = 'Something wrong';
                }
            } else {
                $data['message'] = 'You have already been activated! Try Log in!';
            }
            
                $this->load->view('header',$data);
                $this->load->view('notice',$data);
                $this->load->view('footer');
            
    
        }

}


