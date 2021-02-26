<?php

    session_start();

    //Checking whether the user is logged in or not
    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!==true)
        header("location:login.php");
    else
    {
        require_once "config.php";
        $username = $_SESSION['username'];
        // echo $username;
        $result = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");
    }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/profile.css?v=<?php echo time(); ?>">
    <title>MYAPP</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="welcome.php">MYAPP</a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                </ul>
                <div class="pull-right">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://img.icons8.com/metro/26/000000/guest-male.png"> <?php echo "Welcome ". $_SESSION['username']?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </nav>
    <div class="main">
        <div class="leftside">
            <div class="img_card">
                <img class="image" src="images/avataruser.png">
            </div>
            <div class="username">
                <?php 
                while($row = mysqli_fetch_array($result))
                {
                ?>
                <div> <?php echo $row["username"] ?></div>
                
            </div>
        </div>

        <div class="rightside">
                <div class="detail_card">
                        <div class="detail">
                            <div class="label">EMAIL</div>
                            <div class="response"> <?php echo $row["email"] ?></div>
                        </div>
                        <div class="detail">
                            <div class="label">Phone No.</div>
                            <div class="response"> <?php echo $row["phone"] ?></div>
                        </div>
                        <div class="detail">
                            <div class="label">Joined On</div>
                            <div class="response"> <?php echo date_format(date_create($row["created_at"]),"d/m/Y H:i:s") ?></div>
                        </div>
                    <?php }
                    ?>
                </div>
        </div>

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
  </body>
</html>