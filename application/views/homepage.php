<div id="homepage-carousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#homepage-carousel" data-slide-to="0" class="active"></li>
        <li data-target="#homepage-carousel" data-slide-to="1"></li>
        <li data-target="#homepage-carousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item my-2  active">
            <img class="d-block mx-auto carouselImg" src="<? echo base_url().$latestVideos[0]['coverLocation']; ?>" alt="First slide">
            <div class="carousel-caption d-none d-md-block">
                <a style="color:white" class="text-decoration-none" href="<?echo base_url().'video/index/'.$latestVideos[0]['id'];?>">
                    <h5><?echo $latestVideos[0]['title'];?></h5>
                </a>
            </div>
        </div>
        <div class="carousel-item my-2">
            <img class="d-block mx-auto carouselImg" src="<? echo base_url().$latestVideos[1]['coverLocation']; ?>" alt="Second slide">
            <div class="carousel-caption d-none d-md-block">
                <a style="color:white" class="text-decoration-none" href="<?echo base_url().'video/index/'.$latestVideos[1]['id'];?>">
                    <h5><?echo $latestVideos[1]['title'];?></h5>
                </a>
            </div>
        </div>
        <div class="carousel-item my-2">
            <img class="d-block mx-auto carouselImg" src="<? echo base_url().$latestVideos[2]['coverLocation']; ?>" alt="Third slide">
            <div class="carousel-caption d-none d-md-block">
                <a style="color:white" class="text-decoration-none" href="<?echo base_url().'video/index/'.$latestVideos[2]['id'];?>">
                    <h5><?echo $latestVideos[2]['title'];?></h5>
                </a>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#homepage-carousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#homepage-carousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>


<?  
    function returnCategory($name, $category){
        $videos = "";
        foreach ($category as $videoIndex => $video) {
            $videos .= returnVideo($video);
        }
        
        return '
            <div class="categoryDisplay w-100 d-flex flex-column justify-content-around mt-3" id="category1">
                <div class="d-flex border-bottom pb-2">
                    <div class="ml-4 my-auto pt-2"><h3>'.$name.' <span class="badge badge-secondary">
                        Top'.count($category).'
                    </span></h3></div>
                    <a href="'.base_url().'category/index/'.$name.'" class="btn btn-primary ml-auto mr-4 my-auto" role="button">See More...</a>
                </div>
                <div class="w-100 d-flex categoryDisplayBox">
                        '.$videos.'
                </div> 
            </div>
        ';
    }
    include 'functions.php';
    foreach ($all as $name => $category) {
        echo returnCategory($name, $category);
    }
    
?>
<script>
    <? if (isset($username)) {
        echo 'Cookies.set("name","'.$username.'")';
    } else {
        echo 'console.log("not set");';
    }
    ?>
</script>
