<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午6:12
 */
require_once 'Configure/configure.php';
require_once 'Login/Login.php';
require_once 'Database/DBConnection.php';
session_start();
$login = new Login;
if ($login->userHasLoginIn() && $_GET['from'] != 'user') {
    echo 'login in...';
//    header("Location: user.php");
    echo "<script>setTimeout(window.location.href='/user.php',1000);</script>";
//    include 'user.php';
} else
    include 'login_in.php';
