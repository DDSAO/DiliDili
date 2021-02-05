<div>
    <div class="w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<? echo base_url()."homepage"?>">Homepage</a></li>
                <li class="breadcrumb-item active" aria-current="page"><? echo $category ?></li>
            </ol>
            </nav>
    </div>
    <div class="w-100 d-flex flex-wrap" id="videosBox">
        <?
        foreach($videos as $video){
            echo returnVideo($video);
        }
        ?>
    </div>
    <div class="w-100 d-flex justify-content-center p-3 m-2 hide" id="loading">Loading More ...</div>
</div>

<script>

$(document).ready(function() {
    const delay = 3000;
    var loaded=<?echo $loaded?>;
    console.log('last time loaded '+loaded+" videos")
    console.log('last time scrolled to '+ Cookies.get("scroll-"+"<?echo $category;?>"))
    $(window).scroll(function() {
        Cookies.set("loaded-"+"<?echo $category;?>", loaded);
        Cookies.set("scroll-"+"<?echo $category;?>", window.scrollY );
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('#loading').removeClass('hide');
            setTimeout(() => {
                $.ajax({
                method: "POST",
                url: "<?echo base_url("category/getMore")?>",
                data: JSON.stringify({
                    category: "<?echo $category?>",
                    loaded: loaded,
                }),
                dataType:"json",
            }).done(function(result){
                if (result.length) {
                    loaded += result.length;
                    result.forEach(e=>{
                        $("#videosBox").append(renderVideo(e));
                    })
                    $('#loading').addClass('hide');
                } else {
                    $('#loading').text('All videos have been loaded')
                }       
            })
            }, delay);
        }
    });
   
    if ( Cookies.get("scroll-"+"<?echo $category;?>") !== null ) {
        $(document).scrollTop( Cookies.get("scroll-"+"<?echo $category;?>") );
    }

    // When a button is clicked...
    $('a').click(function(){
        console.log($(document).scrollTop() )
        Cookies.set("loaded-"+"<?echo $category;?>", loaded);
        Cookies.set("scroll-"+"<?echo $category;?>", $(document).scrollTop() );
    });

    function renderVideo(videoInfo){
        return '\
        <a href="'+'<?echo base_url('video/index/');?>'+videoInfo.id+'" class="m-2 w-100 card text-decoration-none d-flex flex-row" >\
            <img src="'+'<?echo base_url();?>'+videoInfo.coverLocation+'" >\
            <div class="card-body">\
                <h5 class="card-title">'+videoInfo.title+'</h5>\
                <p class="card-text">Uploader: '+videoInfo.uploaderName+'</p>\
                <p class="card-text">Views: '+videoInfo.views+'</p>\
                <p class="card-text">Likes: '+videoInfo.likes+'</p>\
            </div>\
        </a>\
    ';
    }
});

    
</script>
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

