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
    $username = $password = $confirm_password = $email = $phone = $token = $status = "";

    //Vraibles to store errors
    $username_err = $password_err = $confirm_password_err = $email_err = $phone_err = "";

    //Check the request
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Check if username is empty
        if(empty(trim($_POST["username"])))
            $username_err = "Username cannot be blank";
        
        elseif(empty(trim($_POST["email"])))
            $email_err = "Email cannot be blank";

        elseif(empty(trim($_POST['phone'])))
            $phone_err = "Phone Number cannot be blank";
        
        elseif(strlen(trim($_POST['phone'])) < 10)
            $phone_err = "Invalid mobile number";
        
        // Check for password
        elseif(empty(trim($_POST['password'])))
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
            else
            {
                $sql = "SELECT id FROM users WHERE username = ?";
                $stmt = mysqli_prepare($conn, $sql);
                if($stmt)
                {
                    mysqli_stmt_bind_param($stmt, "s", $param_username);

                    // Set the value of param username
                    $param_username = trim($_POST['username']);

                    // Try to execute this statement
                    if(mysqli_stmt_execute($stmt))
                    {
                        mysqli_stmt_store_result($stmt);

                        //Check that username already exits or not
                        if(mysqli_stmt_num_rows($stmt) == 1)
                            $username_err = "This username is already taken"; 
                        
                        else
                        {
                            $sqlemail = "SELECT id FROM users WHERE email = ?";
                            $stmtemail = mysqli_prepare($conn, $sqlemail);
                            mysqli_stmt_bind_param($stmtemail, "s", $param_email);
                            $param_email = trim($_POST['email']);
                            mysqli_stmt_execute($stmtemail);
                            mysqli_stmt_store_result($stmtemail);
                            if(mysqli_stmt_num_rows($stmtemail) == 1)
                                $email_err = "Account with this email already exist";
                            else
                            {
                                $sqlphone = "SELECT id FROM users WHERE phone = ?";
                                $stmtphone = mysqli_prepare($conn, $sqlphone);
                                mysqli_stmt_bind_param($stmtphone, "s", $param_phone);
                                $param_phone = trim($_POST['phone']);
                                mysqli_stmt_execute($stmtphone);
                                mysqli_stmt_store_result($stmtphone);
                                if(mysqli_stmt_num_rows($stmtphone) == 1)
                                    $phone_err = "Account with this mobile number already exist";
                                else
                                    $phone= trim($_POST['phone']);
                                $email = trim($_POST['email']);
                            }     
                            $username = trim($_POST['username']);
                        }
                    }
                    
                    else
                        echo "Something went wrong";
                }
                mysqli_stmt_close($stmt);
            }
        }

        // If there were no errors, go ahead and insert into the database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($phone_err))
        {
            $sql = "INSERT INTO users (username,email,phone,password,token,status) VALUES (?,?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt)
            {
                mysqli_stmt_bind_param($stmt, "ssssss", $param_username,$param_email,$param_phone,$param_password,$param_token,$param_status);
                $token = bin2hex(random_bytes(15));
                $status = "inactive";
                // Set these parameters
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_email = $email;
                $param_phone = $phone;
                $param_token = $token;
                $param_status = $status;
                // Try to execute the query
                if (mysqli_stmt_execute($stmt))
                    {
                        require_once "PHPMailer/PHPMailer.php";
                        require_once "PHPMailer/SMTP.php";
                        require_once "PHPMailer/Exception.php";
                    
                        $mail = new PHPMailer\PHPMailer\PHPMailer();
                    
                          //Server settings
                        $mail->SMTPDebug = 3;                      // Enable verbose debug output
                        $mail->isSMTP();                                            // Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
                        $mail->Port       = 587;                                    // TCP port to connect to
                        $mail->Username   = '';                     // SMTP username
                        $mail->Password   = '';                               // SMTP password
                        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                        $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                    
                        //Recipients
                        $mail->setFrom('','Mailer');
                        $mail->addAddress($email);     // Add a recipient
                    
                        // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'Thankyou for registering with us';
                        $mail->Body    = "Click the button below link to verify your account</b>
                        http://localhost/RTDS/Training/Login&SignUPPHP/activate.php?token=$token";
                    
                        if($mail->Send()) {
                            // echo "Mailer Error: " . $mail->ErrorInfo;
                            session_start();
                            $_SESSION["msg"] = "Check your mail to activate your account $email";
                            // $_SESSION["username"] = $username;
                            // $_SESSION["id"] = $id;
                            // $_SESSION["loggedin"] = true;
                            header("location:login.php");
                            // echo "Message sent!";
                        } else {
                            
                        }                               
                    }

                else
                    echo "Something went wrong... cannot redirect!";
            
            }
            mysqli_stmt_close($stmt);
        }
    
        //Storing errors
        else
        {
            if($username_err)
                $_SESSION['errors'] = $username_err;
            elseif($email_err)
                $_SESSION['errors'] = $email_err;
            elseif($phone_err)
                $_SESSION['errors'] = $phone_err;
            elseif($password_err)
                $_SESSION['errors'] = $password_err;
            header("Location:register.php");
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
                    
                    <div class="caption">Welcome</div>
                    
                    <?php if (isset($_SESSION['errors'])): ?>
                            <div class="err">
                                <?php echo $_SESSION['errors'] ?>
                            </div>
                    <?php endif; ?>
                </div>

                <div class="inp username">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="inputUsername" placeholder="Username">
                </div>

                <div class="inp email">
                    <i class="fas fa-at"></i>
                    <input type="email" name="email" id="inputEmail" placeholder="Email">
                </div>
                
                <div class="inp phone">
                    <i class="fas fa-phone-alt"></i>
                    <input type="number" name="phone" id="inputNumber" placeholder="Ex-9876543210">
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
                    <button type="submit" class="btn">Signin</button>
                </div>

                <div class="option">
                    Already have an account? <a href="login.php">Login</a>
                </div>

            </form>
        </div>
    </div>
    
  </body>
</html>
