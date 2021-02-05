<div class="onePage">
    <div class="navFrame" >
        <? include("dashboardNav.php") ?>
    </div>
    <div class="mainFrame d-flex">
        
        <div class="dashMenu d-flex flex-column align-items-center">  
            <div id="dashMenuTrigger">Menu</div>  
            <div class="menuBar active" id="profile">Profile</div>
            <div class="menuBar" id="notification">Notification<?echo (intval($hasNew)===1)? ' (New)':'';?></div>
            <div class="menuBar" id="videoManagement">Video Management</div>
            <div class="menuBar" id="changePassword">Change Password</div>
            <div class="menuBar" id="upload">Upload Video</div>
            <div class="menuBar" id="cashOut">Cash out</div>
            <div class="menuBar" id="topUp">Top up</div>
        </div>
        <div class="dashContent">
            <div class="w-100 h-100" id="loading">Loading</div>
            <? include("dashboard/profile.php")?>
            <? include("dashboard/notification.php")?>
            <? include("dashboard/upload.php")?>
            <? include("dashboard/videoManagement.php")?>
            <? include("dashboard/cashOut.php")?>
            <? include("dashboard/changePassword.php")?>
            <? include("dashboard/topup.php")?>
        </div>
    </div>  
</div>
<div class="w-100 h-100 hide" id="popUp">
    <div class="w-100 h-100 background d-flex justify-content-center align-items-center">
        <div class="d-flex flex-column" id="editBox">
            <div class="p-3 border-bottom m-2" id="title">Edit Photo</div>
            <div class="d-flex flex-column align-items-center">
                <div class="d-flex justify-content-center" id="originalPhoto">
                    <input type="file" accept="image/jpg" name="file" id="submitPhoto">
                    <label class="w-100 h-100 d-flex justify-content-center align-items-center" for="submitPhoto" id="submitPhotoLabel">
                        Click here to upload
                    </label>
                </div>
                <div>Image Preview (Click to crop):</div>
                <div style="width:100px;height:100px;overflow:hidden;">
                    <img id="preview" />
                </div>  
                <div class="w-100 d-flex flex-row justify-content-center align-items-center hide" id="resubmit">

                    <label for="submitPhoto" class="m-2 btn btn-primary">Choose another Photo</label>
                    <button class="m-2 btn btn-primary" id="confirmPhoto" disabled>Confirm (Crop it first)</button>
                <div>
                
        </div>  
            
    </div>
    <div class="btn btn-warning" id="cancel">Cancel</div>
</div>


<script src="<?echo base_url('js/jquery.Jcrop.min.js')?>"></script>
<script type='text/javascript'>
    let photoLocation;
    let photoInfo = {};
    $("#submitPhoto").change(function(){
        if($('#submittedPhoto').data('Jcrop') != undefined){
            $('#submittedPhoto').data('Jcrop').destroy()
        }
        
        $("#submitPhotoLabel").text('Submitting')
        let data = new FormData()
        data.append('photo', $('#submitPhoto')[0].files[0])
        data.append('id',<?echo $id;?>)
        fetch('<? echo base_url('dashboard/submitPhoto')?>',{
            method:"POST",
            body:data
        }).then(response=>{
            return response.json()
        }).then(response=>{
            console.log(response)

            if(response.success){
                photoLocation = response.location
                let loadImg = new Promise((resolve)=>{
                    $("#submitPhotoLabel").replaceWith('<img src="'+response.displayLocation+'" id="submittedPhoto" />')
                    $("#submittedPhoto").replaceWith('<img src="'+response.displayLocation+'" id="submittedPhoto" />')
                    let img = new Image()
                    img.src = response.displayLocation
                    img.onload = function() {
                        width = parseInt(this.width * $("#submittedPhoto").height() / this.height)
                        height = $("#submittedPhoto").height()   
                        photoInfo.widthR = this.width / width
                        photoInfo.heightR = this.height / height
                        resolve({width:width,height:height})        
                    }
                }).then((values)=>{
                    if (values.width > values.height) {
                        $("#preview").attr("src",response.displayLocation).css('height','100px')
                    } else {
                        $("#preview").attr("src",response.displayLocation).css('width','100px')
                    }
                    
                    $("#resubmit").removeClass('hide')
                    jQuery(function($) {
                        $('#submittedPhoto').Jcrop({
                            onChange: function(coords){onChange(coords,values.width, values.height)},
                            onSelect: function(coords){onSelect(coords,values.width, values.height)},
                            aspectRatio: 1
                        });
                    }); 
                })
            }
        }).catch(e=>{
            console.log(e)
        })
    })
    $("#confirmPhoto").click(function(){
        let data = {
            'id':<?echo $id;?>,
            'location':photoLocation,
            'w':photoInfo.w,
            'h':photoInfo.h,
            'x':photoInfo.x,
            'y':photoInfo.y,
        }
        fetch('<?echo base_url('dashboard/savePhoto')?>',{
            method:"POST",
            body: JSON.stringify(data),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        }).then(response=>{
            return response.json()
        }).then((response)=>{
            if (response.success === 1) {
               cancel()
               $("#userImage").attr('src','<?echo base_url('files/photos/'.$id.'/icon.jpg?');?>'+String(new Date().getTime()));
            }
        })
    })
    $("#cancel").click(function(){
        cancel()
    })
    function cancel(){
        if($('#submittedPhoto').data('Jcrop') != undefined){
            $('#submittedPhoto').data('Jcrop').destroy()
        }
        $("#submittedPhoto").replaceWith('<label class="w-100 h-100 d-flex justify-content-center align-items-center" \
            for="submitPhoto" id="submitPhotoLabel">Click here to upload </label>')
        if (<? echo file_exists('files/photos/'.$id.'/icon.jpg') ? 'true' : 'false';?>) {
            $("#userImage").attr('src','<?echo base_url('files/photos/'.$id.'/icon.jpg?');?>'+String(new Date().getTime()))
        } else {
            $("#userImage").attr('src','<?echo base_url('img/icon.jpeg');?>')
        }
        
        $("#popUp").addClass('hide')
    }
    function onSelect(coords,width,height){
        var rx = 100 / coords.w;
        var ry = 100 / coords.h;

        $('#preview').css({
            width: Math.round(rx * width) + 'px',
            height: Math.round(ry * height) + 'px',
            marginLeft: '-' + Math.round(rx * coords.x) + 'px',
            marginTop: '-' + Math.round(ry * coords.y) + 'px'
        });
        photoInfo.w = Math.round(coords.w * photoInfo.widthR)
        photoInfo.x = Math.round(coords.x * photoInfo.widthR)
        photoInfo.h = Math.round(coords.h * photoInfo.heightR)
        photoInfo.y = Math.round(coords.y * photoInfo.heightR)
        $("#confirmPhoto").attr('disabled',false)
    }
    function onChange(coords,width,height){
        var rx = 100 / coords.w;
        var ry = 100 / coords.h;

        $('#preview').css({
            width: Math.round(rx * width) + 'px',
            height: Math.round(ry * height) + 'px',
            marginLeft: '-' + Math.round(rx * coords.x) + 'px',
            marginTop: '-' + Math.round(ry * coords.y) + 'px'
        });
        $("#confirmPhoto").attr('disabled',true).text('Submit')
    }

    $("#dashMenuTrigger").click(()=>{
        if($(".menuBar").css("display")=="block"){
            $(".menuBar").css('display','none')
        } else {
            $(".menuBar").css('display','block')
        }
    })
    $(".menuBar").each(function(index, element){
        $(this).click(function(){
            changePage($(this))
        })
    })
    $(".canEdit").click(function(){
        buttonToInput($(this))
    })
    
    /*
    //legacy function
    async function getData(func){
        $('#loading').css('display','block')
        //fake loading time
        //await new Promise((r,j) => setTimeout(r, 2000));
        
        let data = await $.ajax({
            url:'<?=base_url()?>dashboard/'+func,
            method: 'post',
            data: {userName: '<?echo $userName?>'},
            dataType: 'json',
            success: function(response){   
                $('#loading').css('display','none')
                return response
            }
        })
        return data;
    }
    */
    function changePage(thisPage){
        $('.active').removeClass('active')
        thisPage.addClass('active')
        $('.currentPage').removeClass('currentPage')
        //let pageSelector = "#"+thisPage.attr('id')+"Page"
        $("#"+thisPage.attr('id')+"Page").addClass("currentPage")
        
    }
    function buttonToInput(thisButton){
        let currentContent = thisButton.children('span').text()
        let thisClasses = thisButton.attr("class")
        let thisId = thisButton.children('span').attr("id")
        let newInput = '\
        <input type="text" class="form-control" id="'+thisId+'" name="'+thisId+'" \
        value="'+currentContent+'" placeholder="'+thisId+'" originalValue="'+currentContent+'">\
        '
        thisButton.parent().html(newInput)
        $("#"+thisId).addClass(thisClasses).removeClass('btn btn-outline-primary')
        $(".saveChange").css('visibility','visible')
    }
    function inputToButton(){
        $(".canEdit").each(function(){
            if($(this).prop('tagName') === "INPUT") {
                let classes = $(this).attr('class')
                let id = $(this).attr('id')
                let value = $(this).val()!=="" ? $(this).val() : $(this).attr('originalValue')
                let html = 
                '<button class="btn btn-outline-primary '+classes+'"><span id='+id+'>'+value+'</span></button>'
                $(this).replaceWith(html)
            }
        })
        $(".canEdit").click(function(){
            buttonToInput($(this))
        })
        $(".saveChange").css('visibility','hidden')
    }

        
    
            

 </script>