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
    $username = $password = $confirm_password = "";

    //Vraibles to store errors
    $username_err = $password_err = $confirm_password_err = "";

    //Check the request
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Check if username is empty
        if(empty(trim($_POST["username"])))
            $username_err = "Username cannot be blank";
        

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
                        $username = trim($_POST['username']);
                }
                
                else
                    echo "Something went wrong";
            }
            mysqli_stmt_close($stmt);
         }

    


        // Check for password
        if(empty(trim($_POST['password'])))
            $password_err = "Password cannot be blank";

        //Check password length    
        elseif(strlen(trim($_POST['password'])) < 8)
            $password_err = "Password cannot be less than 8 characters";
        
        else
        {
            $password = trim($_POST['password']);

            //Check password is matching with the confirm password field
            if(trim($_POST['password']) !=  trim($_POST['confirm_password']))
                $password_err = "Passwords should match";
        }




        // If there were no errors, go ahead and insert into the database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err))
        {
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt)
            {
                mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

                // Set these parameters
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT);

                // Try to execute the query
                if (mysqli_stmt_execute($stmt))
                    header("location: login.php");

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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>MYAPP</title>
  </head>

  <body>

    <div class="container mt-4">
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
        </form>

        <!-- Code to display error -->
        <?php if (isset($_SESSION['errors'])): ?>
            <div style="width:100%">
                <div class="alert alert-danger alert-dismissible fade show" style="margin:0px auto;text-align:center;width:70%" role="alert">
                    <?php echo $_SESSION['errors'] ?>
                    <button style="border:none"   data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!--Bootstrap script files-->

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

  </body>
</html>
