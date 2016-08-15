<?php

require_once 'Database/DBConnection.php';
require_once 'Configure/configure.php';

$active = null;
$register = null;
if (isset($_GET['active'])) {
    include_once 'Register/Active.php';
    $active = new Active();
} else {
    include_once 'Register/Register.php';
    include_once 'View/register.php';
    $register = new Register();
}


if ($active != null) {
    if ($active->has_active) {
        echo "<script>alert('" . EMAIL_VERIFY_OK . "');" . "setTimeout(window.location.href='index.php', 1000)" .
            "</script>";

    }else
        echo "<script>alert('". EMAIL_VERIFY_FAIL . "');" ."setTimeout(window.location.href-'index.php', 1000)" .
            "</script>";
}

/*
if ($register != null) {
    if ($register->email_sent) {
        echo "<script>alert('" . EMAIL_HAS_SENT . "');" . "setTimeout(window.location.href='index.php', 1000)" .
            "</script>";
    }
}*/