<?php

require_once 'Database/DBConnection.php';
require_once 'Configure/configure.php';
session_start();
$active = null;
$register = null;
if (isset($_GET['active'])) {
    include_once 'Register/Active.php';
    $active = new Active();
    if ($active->has_active) {
        echo "<script>alert('" . EMAIL_VERIFY_OK . "');" . "setTimeout(window.location.href='index.php', 1000)" .
            "</script>";

    }else
        echo "<script>alert('". EMAIL_VERIFY_FAIL . "');" ."setTimeout(window.location.href-'index.php', 1000)" .
            "</script>";
} else {
    include_once 'Register/Register.php';
    $register = new Register();
    include_once 'View/register.php';
}


/*
if ($register != null) {
    if ($register->email_sent) {
        echo "<script>alert('" . EMAIL_HAS_SENT . "');" . "setTimeout(window.location.href='index.php', 1000)" .
            "</script>";
    }
}*/
