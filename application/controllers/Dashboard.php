<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dashboard extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model');
    }

	public function index($id=null)
	{
        $userInfo = $this->session->userdata('userInfo');
        $restoredId = isset($userInfo) ? $userInfo['id'] : get_cookie('id');
        $login = isset($userInfo) ? $userInfo['login'] : get_cookie('login');
        

        if ($login === 1 && intval($restoredId) === intval($id)){
            $data = $this->collectData($id);
            $data['login'] = 1;
            $this->load->view('header',$data);
            $this->load->view('dashboard',$data);
            //$this->load->view('footer');
        } else {
            $data['message'] = "Please Log in first";
            $data['login'] = 0;
            $this->load->view('header',$data);
            $this->load->view('notice',$data);
            //$this->load->view('footer');
        }
    }
    //profile page
    public function collectData($id){
        
        $data  = $this->model->getUserInfo($id);
        
        //todo
        $videos = $this->model->getVideosByUser($id);
        $views = 0;
        $likes = 0;
        foreach($videos as $video){
            $views += $video['views'];
            $likes += count($video['likes']);
        }
        $data['videos'] = $videos;
        $data['views'] = $views;
        $data['likes'] = $likes;
        $data['fans'] = count($data['fans']);
        $data['hasNew'] = $this->model->hasNewNotification($id);
        $data['notification']= $this->model->getNotification($id);
        return $data;
    }
    public function getNewNotification($id){
        $message = array('hasNotification'=>0);
        if ($this->model->hasNewNotification(intval($id))) {
            $notifications = $this->model->getNotification($id);
            $message['hasNotification'] = 1;
            $message['text'] = end($notifications)->text;
        } 
        echo json_encode($message);
    }

    public function submitPhoto(){
        
        $photoDir = $_SERVER['DOCUMENT_ROOT'].'/files/photos/'.$this->input->post('id').'/';
        $photoPath = 'files/photos/'.$this->input->post('id').'/'.$_FILES['photo']['name'];
       
        if (! file_exists($photoDir)) {
            mkdir($photoDir, 0775,true);
        }
    
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoDir.$_FILES['photo']['name'])) {  

           echo json_encode(array(
               'success'=>1,
               'location'=>$_SERVER['DOCUMENT_ROOT'].'/'.$photoPath,
               'displayLocation'=>base_url().$photoPath,
            ));
        } else {
            echo json_encode(array('success'=>0));
        }
    }
    public function savePhoto(){
        
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = (array) json_decode($stream_clean);
        
        $photoPath = $_SERVER['DOCUMENT_ROOT'].'/files/photos/'.$request['id'].'/'.'icon.jpg';
        $targ_w = $targ_h = 150;
        $jpeg_quality = 100;

        $src = $request['location'];
        $img_r = imagecreatefromjpeg($src);
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$request['x'],$request['y'],
            $targ_w,$targ_h,$request['w'],$request['h']);

        imagejpeg($dst_r, $photoPath, $jpeg_quality);
        echo json_encode(array('success'=>1));
        
    }
    public function saveProfile(){
     
        $updateInfo = array(
            'id'=>$this->input->post('id')
        );
        $result = [
            'success' => 0,
            'message' => [],
        ];
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        if ($name !== ""){
            if ($this->model->checkNameUnique($name)){
                $updateInfo += ['name' => $name];
            } else {
                array_push($result['message'],'<p>Save Failed: This name has been taken</p>');
            }
        } 
        if ($description !== "") {
            $updateInfo += ['description' => $description];
        }
        $phoneTest = '/\b\d{10}\b/';
        if ($phone !== "") {
            if (preg_match($phoneTest,$phone))  {
                $updateInfo += ['phone' => $phone];
            } else {
                array_push($result['message'],'<p>Save Failed: Phone number should have exact 10 digits</p>');
            }
        }
        $emailTest = '/[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/';
        if ($email !== "") {
            if (preg_match($emailTest,$email))  {
                $updateInfo += ['email' => $email];
            } else {
                array_push($result['message'],'<p>Save Failed: Please input valid email</p>');
            }
        }
        if (count($result['message'])==0) {
            $queryResult = $this->model->updateUser($updateInfo);
            if($queryResult['success']===1){
                $result['success'] = 1;
                echo json_encode($result);
            }
        } else {
            echo json_encode($result);
        }   
    }
    //changePassword Page
    private function addUserAuth($id){

        $code = "" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
        $this->model->addUserAuth($id, $code);
        return $code;
    }
    private function verifyUserAuth($id,$code){

        $result = $this->model->getUserAuth($id);
        if(count($result) > 0 && $result[0]['code']===strval($code)) {  
            return true;  
        } else {
            return false;
        }
    }
   
    public function verifyByEmail(){
        $id = $this->input->post('id');
        $email = $this->input->post('email');
        $code = $this->addUserAuth($id);
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
        $this->email->subject('[DILIDILI]Change Password');
        $this->email->message('<div>Input this code for changing password</div><div>'.$code.'<div>');
		$sent = $this->email->send();
		if ($sent) {
            echo json_encode(array('success'=>1));
		} else {
			echo json_encode(array(
                'success'=>0,
                'message'=>$this->email->print_debugger(),
            ));
		}
    }
    public function verifyByPhone(){
        $id = $this->input->post('id');
        $phone = $this->input->post('phone');
        $code = $this->addUserAuth($id);
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

        var_dump($outJson);

        //initialise provision
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_HTTPHEADER, array("Content-Type: application/json","cache-control: no-cache","Authorization: Bearer ".$outJson["access_token"]));
        curl_setopt($ch1, CURLOPT_URL,"https://tapi.telstra.com/v2/messages/provisioning/subscriptions");
        curl_setopt($ch1, CURLOPT_POST, true);
        $data = array(
			"activeDays"=>30,
        );
        $dataJson = json_encode($data);

        

		curl_setopt($ch1, CURLOPT_POSTFIELDS,$dataJson);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch1);

        var_dump($server_output);

        curl_close ($ch1);
		//using token to send sms
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer ".$outJson["access_token"],
			"Content-Type: application/json",
		));
		curl_setopt($ch2, CURLOPT_URL,"https://tapi.telstra.com/v2/messages/sms");
		curl_setopt($ch2, CURLOPT_POST, true);
		$data = array(
			"to"=>strval($phone),
			"body"=>"Your code is ".$code.". This message is coming from [DILIDILI]",
        );
		$dataJson = json_encode($data);
		curl_setopt($ch2, CURLOPT_POSTFIELDS,$dataJson);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch2);
        var_dump($server_output);
        curl_close ($ch2);
        $result = json_decode($server_output,true);

        $result['success'] = 1;
        echo json_encode($result);	
    }

    public function changePassword(){

        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $code = $this->input->post('code');
        $message = "";
        if($this->verifyUserAuth($id, $code)){
            $query = array(
                'id' => $id,
                'password' => hash('sha256',$password),
            );
            $this->model->updateUser($query);
            echo json_encode(array('success'=>1));
        } else {
            echo json_encode(array(
                'success' => 0,
                'message' => "<p>the code is not correct</p>",
            ));   
        };
    }
    //upload page
    public function uploadVideo(){

        $vNum = $this->model->getAndAddIndex('videos');

        $videoDir = $_SERVER['DOCUMENT_ROOT'].'/files/videos/'.$this->input->post('id').'/';
        $coverDir = $_SERVER['DOCUMENT_ROOT'].'/files/covers/'.$this->input->post('id').'/';
        $videoPath = 'files/videos/'.$this->input->post('id').'/'.strval($vNum) . '.mp4';
        $coverPath = 'files/covers/'.$this->input->post('id').'/'.strval($vNum) . '.jpeg';
        if (! file_exists($videoDir)) {
            mkdir($videoDir, 0775,true);
        }
        if (! file_exists($coverDir)) {
            mkdir($coverDir, 0775,true);
        }
        if (move_uploaded_file($_FILES['video']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/'.$videoPath)) {  
            $command = 'ffmpeg -i '.$_SERVER['DOCUMENT_ROOT'].'/'.$videoPath.' -vframes 1 -vf scale=240:180 '.$_SERVER['DOCUMENT_ROOT'].'/'.$coverPath;   
            shell_exec($command);
            if(! file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$coverPath)){
                $coverPath = 'asset/music-scaled.png';
            }
            echo json_encode(array(
                'success'=>1,
                'videoNumber'=>$vNum,
                'videoLocation'=>$videoPath,
                'coverLocation'=>$coverPath,
            ));
        } else {
            echo json_encode(array(
                'success' => 0,
                'message' => 'something wrong'
            ));
        }
    }
   
    public function submitVideo(){

        $videoInfo = array(
            'id'=>$this->input->post('id'),
            'title'=>$this->input->post('title'),
            'tags'=>$this->input->post('tags'),
            'description'=>$this->input->post('description'),
            'uploader'=>$this->input->post('uploader'),
            'category'=>$this->input->post('category'),
            'videoLocation'=>$this->input->post('videoLocation'),
            'coverLocation'=>$this->input->post('coverLocation'),
        );
        $result = $this->model->addVideo($videoInfo);
        if ($result['success']===1){
            echo json_encode(array('success' => 1));
        } else {
            echo json_encode(array('success' => 0));
        }
        
        
    }
    //video Management Page
    public function saveVideos(){
 
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = (array) json_decode($stream_clean);
        
        foreach($request as $video){
            $video = (array) $video;
            $query = array('id'=>$video['id']);
            if ($video['title'] !== ""){
                $query['title'] = $video['title'];
            }
            if ($video['tags'] !== ""){
                $query['tags'] = $video['tags'];
            }
            if ($video['description'] !== ""){
                $query['description'] = $video['description'];
            }
            if (count($query) > 1) {
                $this->model->updateVideo($query);
            }
        }
        echo json_encode(array('success'=>1));
        
    } 
    public function delectVideo(){
        $this->load->model('model');
        $userInfo = $this->session->userdata('userInfo');
        if(isset($userInfo) && $userInfo['id']==intval($this->input->post('id') && $userInfo['login'] === 1)){
            $this->model->delectVideo(intval($this->input->post('vid')));
        }
        echo json_encode(array('success'=>1));
    }
    public function topUp(){
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = (array) json_decode($stream_clean);
        if ($request){
            $uid = $request['uid'];
            $amount = $request['amount'];
            $orderId = $request['orderId'];
        } else {
            http_response_code(422);
            echo json_encode(array("error" => "invalid access! You thought you can top up in this way?"));
        }
        
        $this->load->model('model');
        if (isset($orderId)){
            //get token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json","Accept-Language: en_US"));
            curl_setopt($ch, CURLOPT_URL,"https://api.sandbox.paypal.com/v1/oauth2/token");
            curl_setopt($ch, CURLOPT_USERPWD, "Acdl-VQxOvN9M8_shy1VZujwGS31HePjZXr7fSckcCGif7rVRYYWPoJKlJsEJ3f7smiycyb2rRNiJ8K_:EMfqW92USvKwHVb69g8a6A7f-qUFfoq_q3NLFCD6ordfRIsfNnpTJcvZbLjU9tgrnZ9D2WpvsHTQlw_b");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $outJson = json_decode($server_output,true);
            $token = $outJson['access_token'];
            //confirm order status
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL,"https://api.sandbox.paypal.com/v2/checkout/orders/".$orderId."/capture");
            curl_setopt($ch2, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$token));
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $server_output2 = curl_exec($ch2);
            curl_close ($ch2);
            $result = json_decode($server_output2,true);
            if($result['status']==="COMPLETED") {
                $this->model->topUp($uid,$amount);
                $currentAmount = $this->model->getMoney($uid);
                $message = 'top up '.$amount.' successfully, you have '.$currentAmount.' dollars in your acount now';
                echo json_encode(array("message" => $message,"amount"=>$amount,"currentMoney"=>$currentAmount));
            } else {
                http_response_code(422);
                echo json_encode(array("error" => "order not completed","request"=>$request));
            }
        } else {
            http_response_code(422);
            echo json_encode(array("error" => "missing order id","request"=>$request));
        }
        

        
    }
    public function topUpResult(){
        $amount = $this->input->get('amount');
        $currentMoney = $this->input->get('currentMoney');
        if(isset($amount) && isset($currentMoney)) {
            $data['message'] = 'top up '.$amount.' dollars successfully, you have <strong>'.$currentMoney.'</strong> dollars in your acount now';
            $this->load->view('header');
            $this->load->view('notice',$data);
            $this->load->view('footer');
        } 
        
    }
    
    
}

