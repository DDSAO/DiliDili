<?
function getUserImage($id){
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/files/photos/'.$id.'/icon.jpg')) {
        return base_url('files/photos/'.$id.'/icon.jpg');
    } else {
        return base_url('img/icon.jpeg');
    }
}
function renderComment($commentInfo){
    return '
    <div class="row my-2 mx-2 border py-2">
        <img class="col col-2 userIconSmall" src="'.getUserImage($commentInfo->uid).'" />
        <div class="col col-10">
            <div>'.$commentInfo->name.'</div>
            <div>'.date("F d, Y h:i:s",$commentInfo->time).'</div>
            <div class="border-top text-truncate my-2 py-2">
                '.$commentInfo->content.'
            </div>
        </div>
    </div>
    ';
}
?>

<div class="videoFrame customFrame">
    <div class="leftFrame customFrame p-2">
        <!--Title-->
        <h2 class="title"><?echo $title?></h2>
        <!--Video-->
        <div class="m-auto d-flex flex-column align-items-center" id="videoBox">
            <canvas id="danmuBox"></canvas>
            <video <?echo $style;?> preload="auto" controls id="videoPlayer">
                <source src="<?echo base_url().$videoLocation?>" type="video/mp4">
            </video>
        </div>
        <!-- danmu -->
        <div class="w-100 d-flex flex-row align-items-center m-2">
            <input class="form-control" placeholder="say something~" id="newDanmu"></input>
            <a style="color:white" class="btn btn-primary" id="submitDanmu">Submit</a>
        </div>
        <!--Info-->
        <div class="w-100 d-flex border-top py-2">
            <div class="pl-1 mr-auto"><?echo date("F d, Y h:i:s",$time)?></div>
            <div class="d-flex">
                <div class="mx-2"><?echo $views?> Views</div>
                <div class="mx-2"><?echo count($likes)?> Likes</div>
            </div>
        </div>
        <!--Uploader info-->
        <div class="container-fluid py-2 border-top">
            <div class="row">
                <img class="col col-2 userIconSmall" src="<?echo getUserImage($uploader);?>" />
                <div class="col col-5 col-md-7">
                    <div><?echo $uploaderName?></div>
                    <div><?echo $fans;?> Fans</div>
                    <div class="text-truncate"><?echo $udescription?></div>
                </div>
                <?  
                    if($subscribed){
                        $subscribeButton = '<button class="my-1 btn btn-warning" id="unsubscribe" onclick=unsubscribe()>Unsubscribe</button>';
                    } else {
                        $subscribeButton = '<button class="my-1 btn btn-primary" id="subscribe" onclick=subscribe()>Subscribe</button>';
                    }
                    if($liked){
                        $likeButton = '<button class="my-1 btn btn-warning" onclick=unLike() id="unLike">Unlike</button>';
                    } else {
                        $likeButton = '<button class="my-1 btn btn-primary" onclick=like() id="like">Like</button>';
                    }
                 
                    if ($login === 1){
                        if ($uid !== $uploader) {
                            
                            echo'
                            <div class="col col-5 col-md-3 d-flex flex-column align-items-stretch justify-content-around">
                                '.$subscribeButton.'
                                '.$likeButton.'
                                <button class="my-1 btn btn-primary" id="donate" disabled>donate(Not available)</button>
                            </div>';
                        }
                        
                    } else {
                        echo '
                        <div class="col col-5 col-md-3 d-flex flex-column align-items-stretch justify-content-around">
                            <a href="'.base_url('login?redirect=/video/index/'.$vid).'" class="w-100 h-100 btn btn-danger d-flex justify-content-center align-items-center" >Log in first to Interact with Uploader</a>
                        </div>
                        ';
                    }
                ?>
               
                
            </div>
        </div>
        <!--description-->
        <div class="w-100 border-top py-2">
            <div>Video Description</div>
            <div class="my-4 px-2 border-left border-right">
                <? echo $description?>
            </div>
            <div>Tags: <?echo $tags?></div>
        </div>
        <!--Comments-->
        <div class="container-fluid border-top border-bottom" id="commentBox">
            <div class="row border-bottom py-2">Comments</div>
            <?
                if ($login === 1){
                    echo'
                    <div class="row my-2 mx-2 border py-2 px-1">
                        <img class="col col-2 userIconSmall" src="'.getUserImage($uid).'" />
                        <div class="col col-8">
                            <textarea class="form-control h-100" aria-label="With textarea" placeholder="say something?" id="commentText"></textarea>
                        </div>
                        <button class="col col-2 btn btn-primary mr-0" id="submitComment">Submit Comment</button>
                    </div>
                        ';
                } else {
                    echo '
                    <div class="row my-2 mx-2 border py-2 px-1">   
                        <a href="'.base_url('login?redirect=/video/index/'.$vid).'" class="w-100 btn btn-danger">Log in to comment</a>
                    </div>
                    ';
                }
                $commentsReverse = array_reverse($comments) ;
                foreach($commentsReverse as $commentInfo){
                    echo renderComment($commentInfo);
                }
            ?>
        </div>
    </div>
    <div class="rightFrame customFrame p-2">
        <h2 class="title">ヾ(≧▽≦*)o</h2>
        <div class="w-100 d-flex align-items-center justify-content-center border-top py-2">
            <div class="danmuku border container-fluid" id="danmuku">
                <!--danmukus-->
                <div class="row border-bottom">
                    <p class="col col-3 border-right text-center">Time</p>
                    <div class="col col-9 text-center">Danmuku</div>
                </div>
                <?
                foreach ($danmu as $row) {
                    $totalSeconds = intval($row['time']/1000);
                    $minutes = intval($totalSeconds / 60);
                    if ($minutes < 10) {
                        $minutes = '0'.$minutes;
                    }
                    $seconds = $totalSeconds % 60;
                    if ($seconds < 10) {
                        $seconds = '0'.$seconds;
                    }

                    echo '<div class="row">
                            <p class="col col-3 border-right text-center">'.$minutes.':'.$seconds.'</p>
                            <div class="col col-9 text-truncate">'.$row['content'].'</div>
                        </div>';
                }
                ?>
            </div>
        </div>
        <!-- recommendation-->
        <div class="container-fluid border">
            <div class="row py-3 border-bottom pl-2">Recommendation</div>
            <div class="row my-1 recommmendationBox">
                <p>Not finished yet.</p>
            </div>
        </div>
        <!--
        <div class="container-fluid border">
            <div class="row py-3 border-bottom pl-2">You May like...</div>
            <div class="row my-1 recommmendationBox">
                <div class="col col-6">
                    <img class="videoPic" src="<? echo base_url()."img/icon.jpeg"?>" />
                </div>
                <div class="col col-6 pl-1 d-flex flex-column justify-content-around">
                    <div>Title</div>
                    <div>Uploader</div>
                    <div>Views Likes</div>
                </div>

            </div>
        </div>
        -->
    </div>
</div>
<script>
$(document).ready(function(){
    var videoPlayer = document.getElementById('videoPlayer');
    console.log("Video's resolution is " + <?echo $vwidth;?> + " x " + <?echo $vheight;?>);


class Barrage {
    constructor(canvas) {
        this.canvas = document.getElementById(canvas);
        this.canvas.width = $("#videoBox").width();
        this.canvas.height =$("#videoBox").height() - 100;
        $("#videoPlayer").css("top", String(- $("#videoBox").height() + 100)+"px");
        let rect = this.canvas.getBoundingClientRect();
        this.w = rect.right - rect.left;
        this.h = rect.bottom - rect.top;
        this.ctx = this.canvas.getContext('2d');
        this.ctx.font = '20px Microsoft YaHei';
        this.barrageList = [];

        this.paused=true;
    }

    //添加弹幕列表
    shoot(value) {
        let top = this.getTop();
        let color = this.getColor();     
        let offset = this.getOffset(this.ctx.measureText(value).width);
        let width = Math.ceil(this.ctx.measureText(value).width);
        let barrage = {
            value: value,
            top: top,
            left: this.w,
            color: color,
            offset: offset,
            width: width
        }
        this.barrageList.push(barrage);
    }

    //开始绘制
    draw() {
        if (this.barrageList.length) {
            this.ctx.clearRect(0, 0, this.w, this.h);
            for (let i = 0; i < this.barrageList.length; i++) {
                let b = this.barrageList[i];
                if (b.left + b.width <= 0) {
                    this.barrageList.splice(i, 1);
                    i--;
                    continue;
                }
                if (! this.paused) {
                    b.left -= b.offset;
                } 
                
                this.drawText(b);
            }
        }
        
        requestAnimationFrame(this.draw.bind(this));
    }

    //绘制文字
    drawText(barrage) {
        this.ctx.fillStyle = barrage.color;
        this.ctx.fillText(barrage.value, barrage.left, barrage.top);
    }

    //获取随机颜色
    getColor() {
        return '#' + Math.floor(Math.random() * 0xffffff).toString(16);
    }

    //获取随机top
    getTop() {
        return Math.floor(Math.random() * (this.h - 10)) + 10;
    }

    //获取偏移量
    getOffset(width) {
        return ((this.w + width)/300).toFixed(1)
        //return +(Math.random() * 2).toFixed(1) + 1;
    }
    pause(){
        this.paused = true
    }
    play(){
        
        this.paused = false
        console.log(this.paused)
    }
}

    let refreshFreqency = 500
    let barrage = new Barrage('danmuBox');
    barrage.draw();

    let rawDanmu =<?echo json_encode($danmu);?>;
    let fireDanmu = []
    let loadedDanmu = []
    reloadDanmu()

    function reloadDanmu(){
        loadedDanmu = {}
        firedDanmu = []
        rawDanmu.forEach((danmu)=>{
            timeInterval = String(Math.floor(danmu.time /refreshFreqency)*refreshFreqency)
            if (loadedDanmu[timeInterval] == undefined){
                loadedDanmu[timeInterval] = [danmu.content]
            } else {
                loadedDanmu[timeInterval].push(danmu.content)
            }
        })
    }
    
    videoPlayer.addEventListener('timeupdate',function(){
        let timeInterval = String(Math.floor(this.currentTime*2) * 500)
        let textList = loadedDanmu[timeInterval] == undefined ? [] : firedDanmu.includes(timeInterval) ? [] : loadedDanmu[timeInterval]
        firedDanmu.push(timeInterval)
        textList.forEach((t) => {
            barrage.shoot(t);
        })
    })
    
    videoPlayer.addEventListener('pause',function(){
        barrage.pause() 
    })
    videoPlayer.addEventListener('play',function(){
        barrage.play() 
    })
    videoPlayer.addEventListener('ended',function(){
        firedDanmu = []
    })

    $("#submitDanmu").click(function(e){
        e.preventDefault()
        let content = $("#newDanmu").val()
        if (content !== ""){
            $("#submitDanmu").attr('disabled',true).text('submitting...')
            let data = {
                'vid': <?echo $vid;?>,
                'time': Math.round(videoPlayer.currentTime*1000),
                'content': content,
            }
            rawDanmu.push(data)
            let row = '<div class="row">\
                            <p class="col col-3 border-right text-center">New</p>\
                            <div class="col col-9 text-truncate">'+content+'</div>\
                        </div>'
            $("#danmuku").append(row)
            reloadDanmu()

            fetch('<?echo base_url('video/submitDanmu/');?>',{
                method:'POST',
                body: JSON.stringify(data),
                headers: {
                    'content-type': 'application/json'
                }
            }).then(response=>{
                return response.json()
            }).then(response=>{
                $("#submitDanmu").attr('disabled',false).text('submit')
                $("#newDanmu").val('')
            }).catch(e=>console.log(e))
        }  
    })
    $("#submitComment").click(function(){
        let commentInfo = {
            'vid':<?echo $vid;?>,
            'uid':<?echo $uid;?>,
            'content': $("#commentText").val(),
        }
        fetch('<?echo base_url('video/submitComment');?>',{
            method:"POST",
            body: JSON.stringify(commentInfo),
            headers: {
                'content-type': 'application/json'
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if (response.success === 1){
                location.reload()
            }
            
        }).catch(e=>console.log(e))
    })

    
})
function subscribe(){
        let info = {
            'uploader':<?echo $uploader;?>,
            'uid':<?echo $uid;?>,
        }
        fetch('<?echo base_url('video/subscribe');?>',{
            method:"POST",
            body: JSON.stringify(info),
            headers: {
                'content-type': 'application/json'
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if (response.success === 1){
                $("#subscribe").replaceWith('<button class="my-1 btn btn-warning" onclick=unsubscribe() id="unsubscribe">Unsubscribe</button>')
            }
        }).catch(e=>{
            console.log(e)
        })
    }
    function unsubscribe(){
        let info = {
            'uploader':<?echo $uploader;?>,
            'uid':<?echo $uid;?>,
        }
        fetch('<?echo base_url('video/unsubscribe');?>',{
            method:"POST",
            body: JSON.stringify(info),
            headers: {
                'content-type': 'application/json'
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if (response.success === 1){
                $("#unsubscribe").replaceWith('<button class="my-1 btn btn-primary" onclick=subscribe() id="subscribe">Subscribe</button>')
            }
            
        }).catch(e=>{
            console.log(e)
        })
    }
    function like(){
        let info = {
            'vid':<?echo $vid;?>,
            'uid':<?echo $uid;?>,
        }
        fetch('<?echo base_url('video/like');?>',{
            method:"POST",
            body: JSON.stringify(info),
            headers: {
                'content-type': 'application/json'
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if (response.success === 1){
                $("#like").replaceWith('<button class="my-1 btn btn-warning" onclick=unLike() id="unLike">Unlike</button>')
            }
            console.log(response)
        }).catch(e=>{
            console.log(e)
        })
    }
    function unLike(){
        let info = {
            'vid':<?echo $vid;?>,
            'uid':<?echo $uid;?>,
        }
        fetch('<?echo base_url('video/unLike');?>',{
            method:"POST",
            body: JSON.stringify(info),
            headers: {
                'content-type': 'application/json'
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if (response.success === 1){
                $("#unLike").replaceWith('<button class="my-1 btn btn-primary" onclick=like() id="like">Like</button>')
            }
        }).catch(e=>{
            console.log(e)
        })
    }

    

</script>
