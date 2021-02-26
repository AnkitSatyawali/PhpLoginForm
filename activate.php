<?php

    session_start();
    if(isset($_SESSION['username']))
    {
        header("location:welcome.php");
        exit;
    }
        require_once "config.php";
    if(isset($_GET['token']))
    {
        $token = $_GET['token'];
        $updatesql = " update users set status='active' where token='$token'";
        $query = mysqli_query($conn,$updatesql);
        if($query)
        {
            if(isset($_SESSION['msg']))
            {
                $_SESSION['msg']="Account activated successfully";
                header('location:login.php');
            }
        }
        else{
            $_SESSION['msg']="Account not activated successfully";
                header('location:register.php');
        }
    }
?>