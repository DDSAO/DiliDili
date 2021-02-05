
<div class="w-100 d-flex flex-wrap" id="videosBox">
    <?
    if (isset($result)){
        if(count($result) === 0) {
            echo '<div class="m-5">Sorry, we dont have anything similar to '.$searchWords.'</div>';
        } else {
            echo 'Here is what we think as being similar to '.$searchWords;
            foreach($result as $video){
                echo returnVideo($video);
        }
    }
    } else {
        echo '<div class="m-5">You are not searching anything</div>';
    }
    
    ?>
</div>
<?
function returnVideo($videoInfo){
    return '
        <a href="'.base_url().'video/index/'.$videoInfo['id'].'" class="m-2 w-100 card text-decoration-none d-flex flex-row" >
            <img src="'.base_url().$videoInfo['coverLocation'].'" >
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
