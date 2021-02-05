<div class="w-100 h-100 content container" id="videoManagementPage">

    <? foreach($videos as $videoInfo) {
        echo renderVideo($videoInfo);
    }
    ?>
    <div class="d-flex justify-content-end">
        <button class="w-25 btn btn-success saveChange" id="save">Save All</button>
    </div>

</div>
<?
function renderVideo($videoInfo){
    return
    '<div class="m-2 p-2 container roundBorder darkYellow" id="v'.$videoInfo['id'].'">
        <div class="row my-2">
            <div class="col-3 d-flex justify-content-center">
                <img class="profileImage" src="'.base_url().$videoInfo['coverLocation'].'"/>
            </div>
            <div class="col-9 d-flex flex-column justify-content-around ml-auto">
                <div class="w-100">
                    <button class="w-100 btn btn-outline-primary text-left canEdit">
                        <span id="title'.$videoInfo['id'].'">'.$videoInfo['title'].'</span>
                    </button>
                </div>
                <div class="w-100">
                    <button class="w-100 btn btn-outline-primary text-left canEdit">
                        <span id="tags'.$videoInfo['id'].'">'.$videoInfo['tags'].'</span>
                    </button>
                </div>
                <p class="m-0">Views '.$videoInfo['views'].' Likes '.count($videoInfo['likes']).'</p>
            </div>
        </div>
        <div class="row my-2">
            <div class="col">
                <button class="w-100 btn btn-outline-primary text-left canEdit">
                    <span id="description'.$videoInfo['id'].'">'.$videoInfo['description'].'</span>
                </button>
            </div>
            
        </div>
        <div class="row my-2">
            <div class="col-6">
                <a class="w-50 btn btn-success" href="'.base_url('video/index/'.$videoInfo['id']).'">view</a>
            </div>
            <div class="col-6 d-flex">
                <button class="ml-auto w-50 btn btn-danger" id="delect'.$videoInfo['id'].'">Delect</button>
            </div>
        </div>
    </div> 
    ';
    
}
?>
<script>
    let videos =<?echo json_encode($videos);?>;
    $("#save").click(function(e){
        e.preventDefault();
        data = {}
        videos.forEach((e,index)=>{
            data[index] = {
                'id':e.id,
                'title': $("#title"+e.id).val(),
                'tags': $("#tags"+e.id).val(),
                'description': $("#description"+e.id).val(),
            }
        })
        fetch('<?echo base_url('dashboard/saveVideos')?>',{
            method:"POST",
            body: JSON.stringify(data),
            header: {
                'content-type':'application/json',
            }
        }).then(response=>{
            return response.json()
        }).then(response=>{
            if(response.success === 1){
                inputToButton()
            }
        })
    })
    videos.forEach((e)=>{
        $("#delect"+e.id).click(function(){
            let data = new FormData()
            data.append('id',<?echo $id;?>)
            data.append('vid', e.id)
            fetch('<?echo base_url('dashboard/delectVideo')?>',{
                method:'POST',
                body:data,
            }).then(response=>{
                return response.json()
            }).then(response=>{
                if (response.success === 1) {
                    $("#v"+e.id).replaceWith('')
                }
            })
        })
    })
       
</script>


