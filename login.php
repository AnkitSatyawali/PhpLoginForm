<?php
    session_start();

    //Checking whether the user is logged in or not
    if(isset($_SESSION['username']))
    {
        header("location:welcome.php");
        exit;
    }

    //Checking the previous stored errors
    if(isset($_SESSION['count']) && isset($_SESSION['errors']))
    {
        session_destroy();
        // header("location:register.php");
    }
    
    //Variable to check the previously stored errors
    else
    $_SESSION['count'] = "gotit";

    require_once "config.php";
    
    $username = $password = "";
    $username_err = $password_err = "";
    
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Username cannot be blank";
        }
    
        else
        {
            $username = trim($_POST['username']);
        }

        //check if the password is empty
        if(empty(trim($_POST['password']))){
            $password_err = "Password cannot be blank";
        }

        //Check the password length
        elseif(strlen(trim($_POST['password'])) < 8){
            $password_err = "Password cannot be less than 8 characters";
        }
        //Store password
        else
        {
            $password = trim($_POST['password']);
        }

        //If there is not any error
        if(empty($username_err) && empty($password_err))
        {
            $sql = "SELECT id,username,password,status FROM users WHERE username=?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt)
            {
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set these parameters
                $param_username = $username;

                // Try to execute the query
                if (mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1)
                    {
                        mysqli_stmt_bind_result($stmt,$id,$username,$hashed_password,$status);
                        if(mysqli_stmt_fetch($stmt))
                        {
                            //Verifying password
                            if(password_verify($password,$hashed_password))
                            {
                                if($status=="active")
                                {
                                session_destroy();
                                session_start();
                                $_SESSION["username"] = $username;
                                $_SESSION["id"] = $id;
                                $_SESSION["loggedin"] = true;
                                header("location:welcome.php");
                                }
                                else
                                $_SESSION['msg']="Please activate your account first";
                            }
                            else
                            {
                                $password_err = "Your username or password is incorrect";
                                $_SESSION['errors'] = $password_err;
                            }
                        }
                    }      
                    else
                    {
                    // $username = trim($_POST['username']);
                        $username_err = "User with this username does not exist";
                        $_SESSION['errors'] = $username_err;
                    }
                }
                else
                {
                    echo "Something went wrong... cannot redirect!";
                }
            }
            mysqli_stmt_close($stmt);
        }
        
        //Storing errors in session
        else
        {
            if($username_err)
            $_SESSION['errors'] = $username_err;
            elseif($password_err)
            $_SESSION['errors'] = $password_err;
            header("Location:login.php");
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
    <link rel="stylesheet" type="text/css" href="styles/register.css?v=<?php echo time(); ?>">
    <title>MYAPP</title>
    <style type="text/css">

        input{
            height:3rem;
        }
        i{
            padding-top:1.2rem;
        }

    </style>
  </head>
  
  <body>
    

  <div class="main">
        <img class="background" src="images/wave.png">

        <div class="leftside">
            <img class="leftimage" src="images/bg.svg">
        </div>

        <div class="rightside">
            <form class="form" action="" method="post">
                <div class="usericon">
                    <img class="avatar" src="images/avatar.svg">
                    
                    <div class="caption">Welcome Again</div>
                    
                    <?php if (isset($_SESSION['errors'])): ?>
                            <div class="err">
                                <?php echo $_SESSION['errors'] ?>
                            </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['msg'])): ?>
                            <div class="register_msg">
                                <?php echo $_SESSION['msg'] ?>
                            </div>
                    <?php endif; ?>
                </div>

                <div class="inp username">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="inputUsername" placeholder="Username">
                </div>

                <div class="inp password">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="inputPassword" placeholder="Password">
                </div>
                

                <div class="submit-btn">
                    <button type="submit" class="btn">Login</button>
                </div>
                <div class="option">
                    <a href="resetpassword.php">Forgot Password ?</a>
                </div>        
                <div class="option">
                    Don't have an account yet? <a href="register.php">Create One</a>
                </div>

            </form>
        </div>
    </div>

  </body>
</html>
