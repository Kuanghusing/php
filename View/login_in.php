<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午10:41
 */


?>

<html>
<head>
    <title>Login in</title>
    <link type="text/css" rel="stylesheet" href="css/login_in.css"/>
    <?php
    if (isset($login->errors) && !empty($login->errors['important'])) {
        echo '<script>alert("' . $login->errors[0] . '")</script>';
    }
    ?>
    <script>function reload_captcha() {
            document.getElementById('captcha_img').setAttribute('src', 'Util/get_captcha.php?r=' + Math.random());
        }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

</head>
<body>
<div class="main">
    <form method="post" action="index.php">
        <table cellpadding="10px">
            <caption style="text-align: center;"><h1>Login in</h1></caption>
            <tr>
                <td class="td_title"><label for="email">Email</label></td>
                <td class="td_input"><input type="email" id="email" name="email" placeholder="xxx@xx.xx" required
                                            value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>"/></td>
                <td><span
                        class="error"><?php echo isset($login->errors['email']) ? $login->errors['email'] : "" ?></span>
                </td>
            </tr>
            <tr>
                <td class="td_title"><label>Password</label></td>
                <td class="td_input"><input type="password" id="password" name="password" required></td>
                <td><span class="error"><?php echo isset($login->errors['pwd']) ? $login->errors['pwd'] : "" ?></span>
                </td>
            </tr>
            <tr>
                <td class="td_title"><label>Captcha</label></td>
                <td class="td_input"><input type="text" id="captcha" name="captcha" required></td>
                <td><img src="Util/get_captcha.php" alt="captcha" onclick="reload_captcha()" id="captcha_img"></td>
                <td><span
                        class="error"><?php echo isset($login->errors['captcha']) ? $login->errors['captcha'] : "" ?></span>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2"><input type="checkbox" name="remember_me" id="remember_me"><label for="remember_me">Remember
                        me for a week</label></td>
            </tr>

        </table>
        <div style="width: 100%;margin: 0 auto;">
            <input type="submit" value="Login" name="login" id="login">

            <button class="button" id="register"><a href="reset_password.php"
                                                    style="text-decoration: none;">Register</a></button>

            <button class="button" id="reset_password"><a href="index.php?reset_password">Forget pwd?</a>
            </button>
        </div>
    </form>
</div>
</body>
</html>

