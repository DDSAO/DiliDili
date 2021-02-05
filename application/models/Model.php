<?
class Model extends CI_Model {

public $array = array();

public function getUsers() {
    $query = $this->mongo_db->get('users');
    return $query;
}


public function verifyUser($name, $password) {   
    $nameQuery   = array('name'=>$name); 
    $userInfos = $this->mongo_db->where($nameQuery)->find_one('users');
    if (empty($userInfos)){
        return array('login'=>0,'message'=>'User Name not found');
    } else{
        $userInfo = $userInfos[0];
        if ($userInfo['password'] === hash('sha256',$password)){
            $userInfo = array_merge($userInfo,array('login'=>1));
            return $userInfo;
        } else {
            return array('login'=>0,'message'=>'Password not matched');
        }
    }
}
public function isActivated($id){
    $user = $this->mongo_db->get_where('users',array('id'=>intval($id)));
    if (count($user) > 0 && $user[0]['activated'] === 1){
        return true;
    }
    return false;
}

public function addUser($userInfo){
    if ($this->checkNameUnique($userInfo['name'])){
        if ($this->checkEmailUnique($userInfo['email'])) {
            $id = $this->getAndAddIndex('users');
            $queryArray = [
                'id' => $id,
                'name' => $userInfo['name'],
                'password' => hash('sha256',$userInfo['password']),
                'email' => $userInfo['email'],
                'phone' => $userInfo['phone'],
                'description' => 'description',
                'fans'=>[],
                'subscribes'=>[],
                'money' => 0,
                'activated' => 0,
            ];
            $this->mongo_db->insert('users', $queryArray);
            $this->initNotification($id);
            return ['success' => 1,'email'=>$userInfo['email'],'id'=>$id];
        } else {
            $message = 'The email has been taken';
        } 
    } else {
        $message = 'The name has been taken';
    }
    return [
        'success' => 0,
        'message'=>$message,
    ];
    
}
public function checkNameUnique($name){
    $nameQuery   = array('name'=>$name); 
    $userInfos = $this->mongo_db->where($nameQuery)->find_one('users');
    if (empty($userInfos)) {
        return true;
    } else {
        return false;
    }
}
public function checkEmailUnique($email){
    $nameQuery   = array('email'=>$email);
    $userInfos = $this->mongo_db->where($nameQuery)->find_one('users');
    if (empty($userInfos)) {
        return true;
    } else {
        return false;
    }
}
public function activateUser($id, $code){
    $query = $this->mongo_db->get_where('userAuth',array('id'=>intval($id)));
    if ($query) {
        $result = $query[0];
    }
    if ($result['code']===$code){
        if ($this->mongo_db->get_where('users',array('id'=>intval($result['id'])))[0]['activated'] === 0) {
            $this->mongo_db->where(array('id'=>intval($result['id'])))->set('activated', 1)->update('users');
            $this->mongo_db->where(array('id'=>intval($id)))->delete('userAuth');
            $this->addNotification(intval($id),"You have successfully activated your account, enjoy! (๑•̀ㅂ•́)و✧");
            return true;
        }
    }
    return false;
}

public function getUserInfo($id){
    $idQuery   = array('id'=>intval($id)); 
    $userInfos = $this->mongo_db->where($idQuery)->find_one('users');
    if(count($userInfos) > 0) {
        return $userInfos[0];
    } else {
        return ['success'=>0];
    }
    
}
public function updateUser($updateQuery){
    $idQuery   = array('id'=>intval($updateQuery['id']));
    unset($updateQuery['id']);
    $this->mongo_db->where($idQuery)->set($updateQuery)->update('users');
    return ['success'=>1];
}

public function searchId($name){
    $query = $this->mongo_db->get_where('users',array('name'=>$name));
    if ($query) {
        return $query[0]['id'];
    } else {
        return null;
    }  
}
public function searchUser($name){
    $query = $this->mongo_db->get_where('users',array('name'=>$name));
    if ($query) {
        return $query[0];
    } else {
        return null;
    }  
}
public function searchUserById($id){
    $query = $this->mongo_db->get_where('users',array('id'=>intval($id)));
    if ($query) {
        return $query[0];
    } else {
        return null;
    }  
}

public function getVideos() {
    $query = $this->mongo_db->get('videos');
    return $query;
}


public function addVideo($videoInfo){
    
    $queryArray = [
        'id' => intval($videoInfo['id']),
        'title' => $videoInfo['title'],
        'tags' => $videoInfo['tags'],
        'description' => $videoInfo['description'],
        'uploader' => intval($videoInfo['uploader']),
        'videoLocation' => $videoInfo['videoLocation'],
        'coverLocation' => $videoInfo['coverLocation'],
        'category' => $videoInfo['category'],
        'views' => 0,
        'likes' => array(),
        'score' => 0,
        'time' => intval(time()),
        'comments' => array(),
    ];
    $this->mongo_db->insert('videos', $queryArray);
    $uploader = $this->searchUserById(intval($videoInfo['uploader']));
    $message = 'Your subscribed uploader <'.$uploader['name'].'> just published a new video <'.$videoInfo['title'].
        '>. <a href="'.base_url('video/index/'.intval($videoInfo['id'])).'">Have a Look!</a>';
    foreach($uploader['fans'] as $fan){
        $this->addNotification(intval($fan->id),$message);
    }
    return ['success' => 1];
}


public function getVideo($videoNumber) {
    $idQuery   = array('id'=>intval($videoNumber)); 
    $videoInfos = $this->mongo_db->where($idQuery)->find_one('videos');
    
    if(count($videoInfos) > 0) {
        $result = $videoInfos[0];
        $result['uploaderName'] = $this->mongo_db->get_where('users',array('id'=>$result['uploader']))[0]['name'];
        foreach($result['comments'] as $key=>$comment){
            $user = $this->mongo_db->get_where('users',array('id'=>$comment->uid))[0];
            $result['comments'][$key]->name = $user['name'];
        }
        $result['success'] = 1;
        return $result;
    } else {
        return ['success'=>0,'id'=>$videoNumber];
    }
}


public function updateVideo($updateQuery){
    $idQuery   = array('id'=>intval($updateQuery['id']));
    unset($updateQuery['id']);
    $this->mongo_db->where($idQuery)->set($updateQuery)->update('videos');
}

public function addComment($commentInfo){
    $query = $this->mongo_db->where(array('id'=>$commentInfo['vid']))->push('comments', array(
        'uid'=>$commentInfo['uid'],
        'time'=>intval(time()),
        'content'=>$commentInfo['content']
        ))->update('videos');
}

public function addView($videoNumber) {
    $idQuery   = array('id'=>intval($videoNumber)); 
    $this->mongo_db->where($idQuery)->inc(array('views'=>1))->update('videos');
}
public function delectVideo($videoNumber){
    $idQuery   = array('id'=>intval($videoNumber)); 
    $this->mongo_db->where($idQuery)->delete('videos');
}

public function getAndAddIndex($type){
    $index = $this->mongo_db->get_where('indexes',array('type'=>$type))[0]['index'];
    $this->mongo_db->where(array('type'=>$type))->inc(array('index'=>1))->update('indexes');
    return $index;
}
public function resetDatabase(){
    $this->mongo_db->drop_db('users');
    $this->mongo_db->drop_db('videos');
    $this->mongo_db->drop_db('indexes');
    $this->mongo_db->drop_db('userAuth');
    $this->mongo_db->drop_db('danmu');
    $this->mongo_db->drop_db('notification');

    $this->mongo_db->insert('indexes',array(
        'type'=>'users',
        'index' => 0,
    ));
    $this->mongo_db->insert('indexes',array(
        'type'=>'videos',
        'index' => 40,
    ));
    $user1 = array(
        'name' => 'admin',
        'password' => 'admin',
        'email' => 'admin@admin.com',
        'phone' => '1234567890',
    );
    $user2 = array(
        'name' => 'visitor',
        'password' => 'visitor',
        'email' => 'visitor@visitor.com',
        'phone' => '1234567890',
    );
    $this->addUser($user1);
    $this->addUser($user2);
    $this->mongo_db->where(array('id'=>0))->set('activated', 1)->update('users');
    $this->mongo_db->where(array('id'=>1))->set('activated', 1)->update('users');
    $video0 = array(
        'id' => 0,
        'title' => 'Triple Ninja',
        'tags' => 'Zed, Dance',
        'description' => 'Default funny video',
        'uploader' => 0,
        'videoLocation' => 'files/default/0.mp4',
        'coverLocation' => 'files/default/0.jpeg',
        'category' => 'funny',
    );
    $video1 = array(
        'id' => 0,
        'title' => 'Chiken With Link',
        'tags' => 'Link, Chicken',
        'description' => 'Default animal video',
        'uploader' => 0,
        'videoLocation' => 'files/default/1.mp4',
        'coverLocation' => 'files/default/1.jpeg',
        'category' => 'animal',
    );
    $video2 = array(
        'id' => 0,
        'title' => 'Happy Ending',
        'tags' => 'Music',
        'description' => 'Default music video',
        'uploader' => 0,
        'videoLocation' => 'files/default/2.mp4',
        'coverLocation' => 'files/default/2.jpeg',
        'category' => 'music',
    );
    $video3 = array(
        'id' => 0,
        'title' => 'Havana',
        'tags' => 'Dance, Gril',
        'description' => 'Default Other video',
        'uploader' => 0,
        'videoLocation' => 'files/default/3.mp4',
        'coverLocation' => 'files/default/3.jpeg',
        'category' => 'other',
    );
    $videos = [$video0, $video1, $video2, $video3];
    for ($i = 0; $i < 4; $i++){
        $videoInfo = $videos[$i];
        for ($j = 0; $j < 10; $j++){
            $video = $videoInfo;
            $video['id'] = intval(($i * 10 )+ $j);
            $video['title'] = $videoInfo['title'].$j;
            $this->addVideo($video);
        }
    }
    function deleteAll($dir) {
        foreach(glob($dir . '/*') as $file) {
            
            if(is_dir($file))
                deleteAll($file);
            else
                unlink($file);
                
        }
    }
    deleteAll('files/photos');
    deleteAll('files/covers');
    deleteAll('files/videos');

}
public function addUserAuth($id,$code){
    if (count($this->mongo_db->get_where('userAuth',array('id'=>intval($id))))===0) {
        $this->mongo_db->insert('userAuth',array(
            'id'=>intval($id),
            'code' => $code,
        ));
    } else {
        $this->mongo_db->where(array('id'=>intval($id)))->set(array('code'=>$code))->update('userAuth');
    }
}
public function addDanmu($danmuInfo){
    $this->mongo_db->insert('danmu',array(
        'vid' => intval($danmuInfo['vid']),
        'time' => $danmuInfo['time'],
        'content' => $danmuInfo['content'],
    ));
}
public function getDanmu($vid) {
    $query = $this->mongo_db->get_where('danmu',array('vid'=>intval($vid)));
    return $query;
}

public function getVideosByUser($id){
    $query = $this->mongo_db->get_where('videos',array('uploader'=>intval($id)));
    return $query;
}
public function getVideosByCategory($category,$offset,$amount){
    $videos = $this->mongo_db->offset(intval($offset))->limit(intval($amount))->get_where('videos',array('category'=>$category));
    foreach($videos as $key=>$video){
        $videos[$key]['uploaderName'] = $this->mongo_db->get_where('users',array('id'=>$video['uploader']))[0]['name'];
    }
    return $videos;
}
public function getHomepageVideos(){
    $categories = ['funny','animal','music','other'];
    $result = array();
    $videos =  $this->mongo_db->get('videos');
    foreach($videos as $video){
        $score = intval($video['views']);
        $this->mongo_db->where(array('id'=>$video['id']))->set(array('score'=>$score))->update('videos');
    }
    foreach($categories as $category){
        $result[$category] =  $this->mongo_db->where(array('category'=>$category))->
            order_by(array('score' => 'DESC'))->limit(5)->get('videos');
        foreach($result[$category] as $key=>$video){
            $result[$category][$key]['uploaderName'] = $this->mongo_db->get_where('users',array('id'=>$video['uploader']))[0]['name'];
        }

    }
    return $result;
}
public function isSubscribe($idleId,$fanId){
    $idles = $this->mongo_db->get_where('users',array('id'=>intval($fanId)))[0]['subscribes'];
    
    foreach($idles as $idle){
        if (intval($idle->id)===intval($idleId)){
            return true;
        }
    }
    return false;
}


public function subscribe($info){
    $idle = $this->mongo_db->where(array('id'=>intval($info['uploader'])))->push('fans', array('id'=>intval($info['uid'])))->update('users');
    $fan = $this->mongo_db->where(array('id'=>intval($info['uid'])))->push('subscribes', array('id'=>intval($info['uploader'])))->update('users');
    $this->addNotification(intval($info['uploader']),'You have got a new fan!'.' You have '.
       count($this->mongo_db->get_where('users',array('id'=>intval($info['uploader'])))[0]['fans']).' fans now ヾ(≧∇≦*)ゝ');
}
public function unsubscribe($info){
    $idle = $this->mongo_db->where(array('id'=>intval($info['uploader'])))->pull('fans', array('id'=>intval($info['uid'])))->update('users');
    $fan = $this->mongo_db->where(array('id'=>intval($info['uid'])))->pull('subscribes', array('id'=>intval($info['uploader'])))->update('users');
    $this->addNotification(intval($info['uploader']),'You just lost a fan!'.' You have '.
       count($this->mongo_db->get_where('users',array('id'=>intval($info['uploader'])))[0]['fans']).' fans now  Σ(っ °Д °;)っ');
}

public function isLiked($vid,$uid){
    $likers = $this->mongo_db->get_where('videos',array('id'=>intval($vid)))[0]['likes'];
    foreach($likers as $liker){
        if (intval($liker->id)===intval($uid)){
            return true;
        }
    }
    return false;
}
public function like($info){
    $this->mongo_db->where(array('id'=>intval($info['vid'])))->push('likes', array('id'=>intval($info['uid'])))->update('videos');
    $video = $this->mongo_db->get_where('videos',array('id'=>intval($info['vid'])))[0];
    $likerName = $this->mongo_db->get_where('users',array('id'=>intval($info['uid'])))[0]['name'];
    $this->addNotification(intval($video['uploader']), 'A user <'.$likerName. '> liked your video '.$video['title'].' ! ψ(｀∇´)ψ');
}
public function unLike($info){
    $this->mongo_db->where(array('id'=>intval($info['vid'])))->pull('likes', array('id'=>intval($info['uid'])))->update('videos');
    $video = $this->mongo_db->get_where('videos',array('id'=>intval($info['vid'])))[0];
    $likerName = $this->mongo_db->get_where('users',array('id'=>intval($info['uid'])))[0]['name'];
    $this->addNotification(intval($video['uploader']), 'A user '.$likerName. 'unliked your video '.$video['title'].' ! ヽ(*。>Д<)o゜');
}


public function getLatestVideos(){
    $query = $this->mongo_db->order_by(array('time' => 'DESC'))->limit(3)->get('videos');
    return $query;
}
public function topUp($uid,$amount){
    $this->mongo_db->where(array('id'=>intval($uid)))->inc(array('money'=>intval($amount)))->update('users');
    $this->addNotification(intval($uid),'You just topped up '.$amount.' dollars');
}
public function getMoney($id){
    $query = $this->mongo_db->get_where('users',array('id'=>intval($id)))[0]['money'];
    return $query;
}

public function searchVideos($name){
    $videos = $this->mongo_db->like("title",$name,"im",true,true)->get("videos");
    foreach($videos as $key=>$video){
        $videos[$key]['uploaderName'] = $this->mongo_db->get_where('users',array('id'=>$video['uploader']))[0]['name'];
    }
    return $videos;
    
}
public function autoFill($name){
    return $this->mongo_db->limit(1)->like("title",$name,"im",true,true)->get("videos");
}
public function initNotification($id){
    $this->mongo_db->insert('notification',array('id'=>intval($id),'messages'=>array(),'hasNew'=>1));
    $this->addNotification(intval($id),'You have created this account! Congra!');
}
public function addNotification($id,$message){
    $row = array('text'=>$message,'time'=>intval(time()));
    $this->mongo_db->where(array('id'=>intval($id)))->push('messages', $row)->update('notification');
    $this->mongo_db->where(array('id'=>intval($id)))->set('hasNew', 1)->update('notification');
}
public function getNotification($id){
    $this->mongo_db->where(array('id'=>intval($id)))->set('hasNew', 0)->update('notification');
    return $this->mongo_db->get_where('notification',array('id'=>intval($id)))[0]['messages'];
}
public function hasNewNotification($id){
    $notification = $this->mongo_db->get_where('notification',array('id'=>intval($id)));
    if (! empty($notification)){
        return $notification[0]['hasNew'];
    }
    
}

public function getUserAuth($id){
    $query = $this->mongo_db->get_where('userAuth',array('id'=>intval($id)));
    return $query;
}    
public function getUserAuths(){
    $query = $this->mongo_db->get('userAuth');
    return $query;
}
public function getIndexes(){
    $query = $this->mongo_db->get('indexes');
    return $query;
}
public function getDanmus(){
    $query = $this->mongo_db->get('danmu');
    return $query;
}
public function getNotifications(){
    $query = $this->mongo_db->get('notification');
    return $query;
}
}

?>
