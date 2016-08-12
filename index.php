<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午6:12
 */
require_once 'Configure/configure.php';
require_once 'Login/Login.php';
session_start();
$login = new Login;

if ($login->userHasLoginIn()) {
    include 'user.php';
} else
    include 'login_in.php';