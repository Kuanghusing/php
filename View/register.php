<?php


?>
<html>
<head>
    <title>Register</title>
    <link type="text/css" rel="stylesheet" href="css/register.css"/>
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
    <form method="post" action="../register.php">
        <table cellpadding="10px">
            <caption style="text-align: center;"><h1>Register</h1></caption>
            <tr>
                <td class="td_title"><label for="email">Email</label></td>
                <td class="td_input"><input type="text" id="email" name="email" placeholder="xxx@xx.xx" required
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
                <td class="td_title"><label for="password_repeat">Password repeat</label></td>
                <td class="td_input"><input type="password" id="password_repeat" name="password_repeat"</td>
                <td><span
                        class="error"><?php echo isset($login->errors['pwd_repeat']) ? $login->errors['pwd_repeat'] : "" ?></span>
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


        </table>
        <div style="width: 70%;margin: 0 auto;">
            <input type="submit" value="Register" name="register" id="register">

            <button class="button" id="login"><a href="reset_password.php"
                                                 style="text-decoration: none;">Already Registered?</a></button>

            </button>
        </div>
    </form>
</div>
</body>
</html>