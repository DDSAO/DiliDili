<?  
    //this function can return video card
    function returnVideo($videoInfo){
        return '
            <a href="'.base_url().'video/index/'.$videoInfo['id'].'" class="mx-2 card customCard text-decoration-none" >
                <img src="'.base_url().$videoInfo['coverLocation'].'" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title">'.$videoInfo['title'].'</h5>
                    <p class="card-text">Uploader: '.$videoInfo['uploaderName'].'</p>
                    <p class="card-text">Views: '.$videoInfo['views'].'</p>
                    <p class="card-text">Likes: '.count($videoInfo['likes']).'</p>
                </div>
            </a>
        ';
    }
    
?>