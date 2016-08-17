<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午10:43
 */
session_start();
if (isset($_SESSION['user_name'])) {
    echo "hello, " . $_SESSION['user_name'];

?>
    <p><a href="index.php?logout">logout</a></p>

    <?php
} else {
    echo 'you havn\'t login in';
}
?>
