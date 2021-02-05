
<div class="onePage">
    <div class="title"><h1>Sign up</h1></div>
        <? echo form_open('signup','class="container my-3 p-5"'); 
        
        if(strlen(validation_errors()) > 0) {
            echo '<div class="w-100 errors p-2 mb-1">
                    <p class="text-center errorTitle">Errors do happen (⊙x⊙;)</p>';
            
            $errors = explode('<p>',validation_errors());
            //remove the first empty str after exploding
            array_shift($errors);
 
            foreach($errors as $error){
                echo '❌'.$error;
            }
            echo '</div>';
        } 
            
        ?>
        
        <div class="form-row">
            <div class="form-group col-lg-11 col-9">
                <label for="name">User Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="A Unique User" value="<?echo $_SESSION['name']?>">
            </div>
            <div class="form-group col-lg-1 col-3 d-flex flex-column">
                <label><p class="text-center" id="response">&nbsp</p></label>
                <div class="btn btn-primary" id="check">Check</div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-lg-6">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="<?echo $_SESSION['email']?>">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="phone">Phone</label>
                <input type="number" class="form-control" id="phone" name="phone" placeholder="Mobile Number" value="<?echo $_SESSION['phone']?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-lg-6">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?echo $_SESSION['password']?>">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" class="form-control" id="comfirmPassword" name="confirmPassword" placeholder="Confirm Password" value="<?echo $_SESSION['confirmPassword']?>">
            </div>
        </div>
        
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="agree" name="agree" value=1 <? if($_SESSION['agree']===1){echo "checked";}?>>
            <label>Agree on our Terms and Conditions</label>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Sign up</button>
        </div>
            
        </div>  
    <?php echo form_close(); ?>
</div>
<script>
    $(document).ready(function(){
        var currentChecking;
        $("#check").mouseenter(()=>{
            if (currentChecking !== $('#name').val()){
                $("#check").removeClass('btn-danger btn-success')
                $("#check").addClass('btn-primary')
                $("#check").text("Check")
            }
        })
        $("#check").click(()=>{
            currentChecking = $('#name').val();
            fetch('<? echo base_url();?>signup/checkNameUnique/'+$('#name').val())
            .then((response)=>{
                return response.text().then(function(text){
                    if (text==="ok") {
                        $("#check").removeClass('btn-primary btn-danger');
                        $("#check").addClass('btn-success');
                        $("#check").text(text);
                    } else {
                        $("#check").removeClass('btn-primary btn-success');
                        $("#check").addClass('btn-danger');
                        $("#check").text(text);
                    }
                    
                })
            })
            .catch(error => console.error('Error:', error))
            
        })


    })
</script>