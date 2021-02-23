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
            $sql = "SELECT id,username,password FROM users WHERE username=?";
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
                        mysqli_stmt_bind_result($stmt,$id,$username,$hashed_password);
                        if(mysqli_stmt_fetch($stmt))
                        {
                            //Verifying password
                            if(password_verify($password,$hashed_password))
                            {
                                session_start();
                                $_SESSION["username"] = $username;
                                $_SESSION["id"] = $id;
                                $_SESSION["loggedin"] = true;
                                header("location:welcome.php");
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>MYAPP</title>
  </head>
  
  <body>
    

    <div class="container mt-4">
            <h2 style="text-align:center;margin-bottom:2rem">Welcome Again To MyAPP</h3>
            <h3>Please Login Here: </h3>
            <hr>
        <form action="" method="post">
            <div class="form-group">
                <label for="inputEmail4">Username</label>
                <input type="text" class="form-control" name="username" id="inputEmail4" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="inputPassword4">Password</label>
                <input type="password" class="form-control" name ="password" id="inputPassword4" placeholder="Password">
            </div> 
            <div style="text-align:center">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <h4 style="text-align:center;margin-top:1rem">Not registered yet then register <a href="register.php">here</a></h4>
        </form>
        <?php if (isset($_SESSION['errors'])): ?>
            <div style="width:100%">
                <div class="alert alert-danger alert-dismissible fade show" style="margin:0px auto;text-align:center;width:70%" role="alert">
                    <?php echo $_SESSION['errors'] ?>
                    <button style="border:none"   data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
  </body>
</html>
