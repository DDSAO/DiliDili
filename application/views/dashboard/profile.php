<div class="w-100 h-100 content currentPage" id="profilePage">
    <div class="container-fluid">
        <div class="row my-2">
            <div class="col col-3 col-lg-2 d-flex justify-content-center" id="userImageBox">
                <img id="userImage" class="profileImage" src="<?
                if (file_exists($_SERVER['DOCUMENT_ROOT'].'/files/photos/'.$id.'/icon.jpg')) {
                    echo base_url('files/photos/'.$id.'/icon.jpg');
                } else {
                    echo base_url('img/icon.jpeg');
                }
                ?>"/>  
                <div id="userImageLabel" class="d-flex justify-content-center align-items-center">Edit</div>
            </div>
            <div class="col col-9 col-lg-10 d-flex flex-column justify-content-around ">
                <div class="d-flex">
                    <p class="col-1 col-lg-1 col-sm-2 pl-0 ml-0">Name:</p>
                    <div class="col-3 px-0">
                        <button class="w-100 btn btn-outline-primary canEdit">
                            <span id="name"><? echo $name;?></span>
                        </button>
                    </div>
                    
                    <p class="col-8 col-sm-7 text-right text-truncate">
                        Views <? echo $views;?> 
                        Likes <? echo $likes;?>
                        Fans <? echo $fans;?>
                    </p>
                </div>
            
                <div class="h-50">
                    <button class="h-100 w-100 btn btn-outline-primary canEdit">
                        <span id="description"><? echo $description;?></span>
                    </button>
                </div>
            </div>
        </div> 
    
        <div class="row my-4">
            <div class="col col-3 col-lg-2 d-flex justify-content-center">
                <p>Email:</p>
            </div>
            <div class="col col-9 col-lg-10 ">
                <button class="w-100 btn btn-outline-primary canEdit">
                    <span id="email"><? echo $email;?></span>
                </button>
            </div>
            
        </div>
        <div class="row my-4">
            <div class="col col-3 col-lg-2 d-flex justify-content-center">
                <p>Phone:</p>
            </div>
            <div class="col col-9 col-lg-10 ">
                <button class="w-100 btn btn-outline-primary canEdit">
                    <span id="phone"><? echo $phone;?></span>
                </button>
            </div>
            
        </div>
        <div class="row my-4">
            <div class="col col-8 ml-auto d-flex justify-content-center">
               <div class="w-100 saveChange text-center errorMessage" id="profileMessage"></div>
            </div>
            <div class="col col-3 ml-auto d-flex justify-content-center">
               <button class="w-100 btn btn-success saveChange" id="saveProfile">Save</button>
            </div> 
        </div>
    </div>
</div>

<script>
    
    $("#saveProfile").click(function(){
        let save = saveProfile()
        console.log('click')
    })
    async function saveProfile(){
        let data = new FormData()
        data.append('id',<?echo $id?>);
        data.append('name', $("#name").val())
        data.append('description', $("#description").val())
        data.append('email', $("#email").val())
        data.append('phone', $("#phone").val())

        fetch('<? echo base_url()?>dashboard/saveProfile',{
            method:'POST',
            body: data,
        }).then((response)=>{
            return response.json()
        }).then(response=>{
            console.log(response)
            if( response.success){
                inputToButton()
            } 
            $("#profileMessage").html(response.message)
        })
            
    }
    $("#userImageLabel").click(function(){
        $("#popUp").removeClass('hide')
    })
</script>



