

<div class="onePage">
    <div class="title"><h1>Sign up</h1></div>
    <? echo form_open('signup/submit','class="container my-3 p-5"') ?>
        <div class="form-row">
            <div class="form-group col-lg-11 col-9">
                <? 
                echo form_label('User Name','name');
                $name = array(
                    'name' => 'name',
                    'id' => 'name',
                    'class' => 'form-control',
                    'placeholder' => 'Get a unique user name :>'
                );
                echo form_input($name);
                ?>
            </div>
            <div class="form-group col-lg-1 col-3 d-flex flex-column">
                <label><p class="text-center" id="verifyName">&nbsp</p></label>
                <button class="btn btn-primary">Check</button>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-lg-6">
                <? 
                echo form_label('Email','email');
                $email = array(
                    'name' => 'email',
                    'id' => 'email',
                    'class' => 'form-control',
                    'placeholder' => 'email@example.com'
                );
                echo form_input($email);
                ?>
            </div>
            <div class="form-group col-12 col-lg-6">
                <? 
                echo form_label('Phone','phone');
                $phone = array(
                    'name' => 'phone',
                    'id' => 'phone',
                    'class' => 'form-control',
                    'placeholder' => 'only number',
                    'type' => 'number',
                );
                echo form_input($phone);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-lg-6">
                <? 
                echo form_label('Password','password');
                $password = array(
                    'name' => 'passowrd',
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => 'at least 6 digits',
                );
                echo form_password($password);
                ?>
            </div>
            <div class="form-group col-12 col-lg-6">
                <? 
                echo form_label('Confirm Password','confirmPassword');
                $confirmPassword = array(
                    'name' => 'confirmPassowrd',
                    'id' => 'confirmPassowrd',
                    'class' => 'form-control',
                    'placeholder' => 'input the password again',
                );
                echo form_password($confirmPassword);
                ?>
            </div>
        </div>
        
        <div class="form-check">
            <?
            $agree = array(
                'name' => 'agree',
                'id' => 'agree',
                'value' => 'agree',
                'class' => 'form-check-input'
            );
            echo form_checkbox($agree);
            echo form_label('Agree on our Terms & Conditions','agree');
            ?>
            
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Sign up</button>
        </div>
            
        </div>  
    </form>
</div>
