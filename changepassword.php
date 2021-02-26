<?php
    session_start();
    
    //Checking whether the user is logged in or not
    if(isset($_SESSION['username']))
    {
        header("location:welcome.php");
        exit;
    }

    //Checking the previous stored errors
    if(isset($_SESSION['check']) && isset($_SESSION['errors']))
        session_destroy();

    //Variable to check the previously stored errors
    else
    $_SESSION['check'] = "yes";

    require_once "config.php";

    //Variables to store form input
    $password = $confirm_password= $token= "";

    //Vraibles to store errors
    $password_err = $confirm_password_err =  "";

    //Check the request
    if(isset($_GET['token']))
    {
        $token = $_GET['token'];
        $updatesql = " update users set status='active' where token='$token'";
        $query = mysqli_query($conn,$updatesql);
        if($query)
        {
            
        }
        else{
            header('location:register.php');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Check if username is empty
        // Check for password
        if(empty(trim($_POST['password'])))
            $password_err = "Password cannot be blank";

        //Check password length    
        elseif(strlen(trim($_POST['password'])) < 8)
            $password_err = "Password cannot be less than 8 characters";

        elseif(strlen(trim($_POST['password'])) >= 8)
        {
            $password = trim($_POST['password']);
    
                //Check password is matching with the confirm password field
            if(trim($_POST['password']) !=  trim($_POST['confirm_password']))
                    $password_err = "Passwords should match";  
        }

        // If there were no errors, go ahead and insert into the database
        if(empty($password_err) && empty($confirm_password_err))
        {
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
            $updatesql = "update users set password='$newPassword' where token='$token'";
            $query = mysqli_query($conn,$updatesql);
            if($query)
            {
                $_SESSION['msg']="Password reset successfully";
                header("Location:login.php");
            }
        }
    
        //Storing errors
        else
        {
            if($password_err)
                $_SESSION['errors'] = $password_err;
            
        }
        // mysqli_close($conn);
    }

?>




<!doctype html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/register.css">
    <title>MYAPP</title>
  
  </head>

  <body>

   <!-- <div class="container mt-4">
        <h2 style="text-align:center;margin-bottom:2rem">Welcome To MyAPP</h3>
        <h3>Please Register Here: </h3>
        <hr>
        <form action="" method="post">
            <div class="form-group">
                <label for="inputUsername">Username</label>
                <input type="text" class="form-control" name="username" id="inputUsername" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="inputPassword4">Password</label>
                <input type="password" class="form-control" name ="password" id="inputPassword4" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="inputPassword4">Confirm Password</label>
                <input type="password" class="form-control" name ="confirm_password" id="inputPassword" placeholder="Confirm Password">
            </div>

    
            <div style="text-align:center">
                <button type="submit" class="btn btn-primary">Sign in</button>
            </div>
            <h4 style="text-align:center;margin-top:1rem">Already registered then 
            <a href="login.php">Login</a></h4>
        </form>-->

        <!-- Code to display error -->
       <!-- <?php if (isset($_SESSION['errors'])): ?>
            <div style="width:100%">
                <div class="alert alert-danger alert-dismissible fade show" style="margin:0px auto;text-align:center;width:70%" role="alert">
                    <?php echo $_SESSION['errors'] ?>
                    <button style="border:none"   data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
            </div>
        <?php endif; ?>
    </div>-->

    <!--Bootstrap script files-->
    <div class="main">
        <img class="background" src="images/wave.png">

        <div class="leftside">
            <img class="leftimage" src="images/bg.svg">
        </div>

        <div class="rightside">
            <form class="form" action="" method="post">
                <div class="usericon">
                    <img class="avatar" src="images/avatar.svg">
                    
                    <div class="caption">Reset password</div>
                    
                    <?php if (isset($_SESSION['errors'])): ?>
                            <div class="err">
                                <?php echo $_SESSION['errors'] ?>
                            </div>
                    <?php endif; ?>
                </div>
                
                <div class="inp password">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="inputPassword" placeholder="Password">
                </div>
                
                <div class="inp confirm_password">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="inputConfirmPassword" placeholder="Confirm Password">
                </div>

                <div class="submit-btn">
                    <button type="submit" class="btn">Change password</button>
                </div>

            </form>
        </div>
    </div>
    
  </body>
</html>
