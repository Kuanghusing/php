<?php
require_once 'Login/PasswordReset.php';
require_once 'Database/DBConnection.php';
require_once 'Configure/configure.php';

session_start();
$reset = new Reset();
if ($reset->has_reset_pwd) {
    echo '<script>alert("reset!");setTimeout(window.location.href="index.php", 1500);'.
        '</script>';
} else
    include 'View/reset_password.php';
