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

$email = "";
$email_err="";
$token="";
if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Check if username is empty
        if(empty(trim($_POST["email"]))){
            $email_err = "Please fill the email";
        }
    
        else if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
        {
            $email = trim($_POST['email']);
            $sql = "SELECT * FROM users WHERE email='$email'";
            $query = mysqli_query($conn,$sql);
            // echo $query;
            if(mysqli_num_rows($query))
            {
                $userdata = mysqli_fetch_array($query);
                $token = $userdata['token'];
                echo $token;

            }
            else
            $email_err = "Account with this email id does not exist";
            
        }
        else
        {
            $email_err = "Invalid email";
        }
        if(empty($email_err))
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
                        $mail->Subject = 'Password recovery';
                        $mail->Body    = "Click the button below link to reset your account password<br/>
                        http://localhost/RTDS/Training/Login&SignUPPHP/changepassword.php?token=$token";
                    
                        if($mail->Send()) {
                            // echo "Mailer Error: " . $mail->ErrorInfo;
                            $_SESSION['msg']="Check your email to reset password";
                            // $_SESSION["username"] = $username;
                            // $_SESSION["id"] = $id;
                            // $_SESSION["loggedin"] = true;
                            header("location:login.php");
                            // echo "Message sent!";
                        } else {
                            
                        }                
        }
        else
        {
            // echo $email_err;
            if($email_err)
                $_SESSION['errors'] = $email_err;
        }
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
                    
                    <div class="caption">Welcome</div>
                    <div class="caption">Enter your email</div>
                    <?php if (isset($_SESSION['errors'])): ?>
                            <div class="err">
                                <?php echo $_SESSION['errors'] ?>
                            </div>
                    <?php endif; ?>
                </div>

                <div class="inp email">
                    <i class="fas fa-at"></i>
                    <input type="email" name="email" id="inputEmail" placeholder="Enter Email">
                </div>

                

                <div class="submit-btn">
                    <button type="submit" class="btn">Send</button>
                </div>

            </form>
        </div>
    </div>

  </body>
</html>
