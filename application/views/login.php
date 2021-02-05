<div class="onePage">
    <div class="title"><h1>Log in</h1></div>
    <? echo form_open('login?redirect='.$redirect,'class="container my-3 p-5"');?>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-6 d-flex justify-content-center">
                <img class="loginIcon" src="<? echo base_url()."img/icon.jpeg"?>" />
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8">
                <?
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
                } else if(strlen($message)>0){
                    echo '<div class="w-100 errors p-2 mb-1">
                            <p class="text-center errorTitle">Errors do happen (⊙x⊙;)</p>';
                    echo '❌'.$message;
                    echo '</div>';
                }
                ?>
                
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8 ">
                <input type="text" class="form-control" id="name" name="name" placeholder="User Name" value="<?echo get_cookie('name')?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8 ">
                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="<?echo get_cookie('password')?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group d-flex flex-row justify-content-center align-items-center col-lg-4 col-8 ">
                <input type="checkbox" id="remember" name="remember" value="yes"><label class="mb-0 ml-2" for="remember">Remember Me</label>
            </div>
        </div>

        
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8 ">
                <button type="submit" class="btn btn-success w-100">Log In</button>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8 ">
                <a href="<?echo base_url('login/forgetPassword')?>" class="btn btn-warning w-100">Forget Password</a>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-lg-4 col-8 ">
                <a href="signup" class="btn btn-primary w-100">Sign up new account</a>
            </div>
        </div>
    <? echo form_close(); ?>
</div>
