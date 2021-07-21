<?php
    include 'core/init.php';

    if(empty($_POST) === false){                        //check form has been submitted
        $userNameErr = $passWordErr = $loginErr = '';   //initialise username, password and login error flags
        $username = testInput($_POST['username']);      //get username field and perform against sql injection   
        $password = testInput($_POST['password']);      //get password field and perform against sql injection   

        //validate username input
        if(empty($username)){                       //Check username field empty
            $userNameErr = 'Username is required';
        }else if(userExists($username) === false){  //check if user exists
            $userNameErr = 'Incorrect Username';
        }

        //validate password input
        if(empty($password)){                       //Check of password field is empty
            $passWordErr = 'Password is required';
        }
        if((empty($userNameErr) === true) && (empty($passWordErr) === true)){   //if all error flags are NOT set
            $login = login($username, $password);                               //try to log in, returning user id
            
            if($login === false){                       //check if user id is set
                $loginErr = 'Incorrect Log In Details'; //set Error flag if user id is not set
            }else{
                $_SESSION['user_id'] = $login;          //set a user id session 
                header('Location: index.php');          //redirect to index page
                exit();
            }
        }
    }
?>
<!DOCTYPE HTML>
<html>
    <?php include 'includes/head.php';?>
<body>
    <div class="container">
        <div class="loginwrapper">
            <h5 class="login-header">Pankys Home Monitoring UI</h5>
            <form action="loginform.php" method="POST">
                <!-- Username Input -->
                <div class="form-group username-input">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Email"><i class="fas fa-user"></i>
                    <?php if(isset($userNameErr)){ ?>  <!-- if username input error -->
                        <div>
                            <p class="err-msg"><?php echo $userNameErr; ?></p>  <!-- print error -->
                        </div>
                    <?php } else{?>
                        <div>
                            <br>
                        </div>
                    <?php } ?>
                </div>
                <!-- Password Input -->
                <div class="form-group password-input">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password"><i class="fas fa-lock"></i>
                    <?php if(isset($passWordErr)){ ?>                           <!-- if password input error -->
                        <div>
                            <p class="err-msg"><?php echo $passWordErr; ?></p>  <!-- print error -->
                        </div>
                    <?php } else{?>
                        <div>
                            <br>
                        </div>
                    <?php } ?>
                </div>
                <!-- login validation -->
                <?php if(isset($loginErr)){ ?>                          <!-- if login validation error -->
                    <div>
                        <p class="err-msg"><?php echo $loginErr; ?></p> <!-- print error -->
                    </div>
                <?php } else{?>
                    <div>
                        <br>
                    </div>
                <?php } ?>
                <!-- Login Button -->
                <div class="form-group" class="login-btn-wrapper">
                    <button type="submit" class="btn-primary login-btn" name="submit" role="button">Log In</button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/overall/footer.php'; ?>
</body>
</html>