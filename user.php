<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午10:43
 */
if (isset($_SESSION['user_name']))
    echo "hello, " . $_SESSION['user_name'];