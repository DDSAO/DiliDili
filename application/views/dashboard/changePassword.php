<div class="w-100 h-100 content" id="changePasswordPage">
    <div class="container-fluid">
        <div class="row my-2">
            <div class="col-8 mx-auto">
                <input class="w-100 form-control" placeholder="New Password" id="pwd"/>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-8 mx-auto">
                <input class="w-100 form-control" placeholder="Confirm Password" id="cpwd"/>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-8 mx-auto d-flex">
                <div class="col-6 pl-0" >
                    <button class="w-100 btn btn-outline-primary" id="verifyEmail">Verified by Email</button>
                </div>
                <div class="col-6 pr-0" >
                    <button class="w-100 btn btn-outline-primary" id="verifyPhone">Verified by Phone</button>
                </div>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-8 mx-auto">
                <button type="submit" class="w-100 btn btn-info form-control" id="submitPassword">Confirm Changes</button>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-8 mx-auto">
                <div class="errorMessage d-flex flex-column justify-content-center" id="passwordMessage"></div>
            </div>
        </div>
    </div>
</div>
<script>
    
    $("#verifyEmail").click(function(){
        $(this).prop('disabled',true)
        $("#verifyPhone").replaceWith('<div class="w-100 btn btn-primary" id="readyForInput">Sending email...</div>')
        let verify = verifyByEmail()
        verify.then(()=>{
            $("#readyForInput").replaceWith('<input class="w-100 form-control" id="code" placeholder="Please input the code here"></input>')
        })
    })
    $("#verifyPhone").click(function(){
        $(this).prop('disabled',true)
        $("#verifyEmail").replaceWith('<div class="w-100 btn btn-primary" id="readyForInput">Sending SMS...</div>')
        let verify = verifyByPhone()
        verify.then(()=>{
            $("#readyForInput").replaceWith('<input class="w-100 form-control" id="code" placeholder="Please input the code here"></input>')
        })
    })
    $("#submitPassword").click(function(){
        let message = "";
        if($("#pwd").val() === ""){
            message += '<p>Please input password</p>'
        }
        if($("#cpwd").val() === ""){
            message += '<p>Please input confirm password</p>'
        }
        if($("#pwd").val() !== $("#cpwd").val()){
            message += '<p>The password and comfirmed password not match</p>'
        }
        if($("#code").length === 0){
            message += '<p>Please verify the code in either way first</p>'
        }
        $("#passwordMessage").html(message)
        if(message === "") {
            let data = new FormData()
            data.append('id',<?echo $id;?>);
            data.append('password',$("#pwd").val());
            data.append('code',$("#code").val());

            fetch('<? echo base_url()?>dashboard/changePassword',{
                method:'POST',
                body: data,
            }).then((response)=>{
                return response.json();
            }).then(response=>{
                console.log(response)
                if(response.success === 1){
                    $("#submitPassword").text('change saved')
                    $("#submitPassword").addClass('btn-success')
                    $("#submitPassword").removeClass('btn-primary')
                    $("#submitPassword").prop('disabled',true)
                } else {
                    $("#passwordMessage").html(response.message)
                }
            })
        } 
        
    })
    
    async function verifyByEmail(){
        let data = new FormData()
        data.append('id',<?echo $id;?>);
        data.append('email', '<?echo $email;?>')   
        await fetch('<? echo base_url()?>dashboard/verifyByEmail',{
            method:'POST',
            body: data,
        }).then((response)=>{
            return response.json()
        }).then(e=>{console.log(e)})
    }
    async function verifyByPhone(){
        let data = new FormData()
        data.append('id',<?echo $id;?>)
        data.append('phone', '<?echo $phone;?>')  
        await fetch('<? echo base_url()?>dashboard/verifyByPhone',{
            method:'POST',
            body: data,
        }).then((response)=>{
            return response.text()
        }).then(e=>{console.log(e)})
    }

    
</script>