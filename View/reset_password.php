<html>
<head><title>Reset password</title>
    <style>
        body {
            width: 400px;
            height: 450px;;
            margin: 100px auto;
            text-align: center;
        }

        .button {
            border: none;
            background: darkseagreen;
            margin: 0 auto;
            width: 100px;
            height: 40px;
        }

        .button:hover {
            background-color: cadetblue;
            color: white;
        }

        .error {
            color: red;
        }

        input {
            border: none;
            width: 170px;
            height: 35px;
        }

        #main {
            background-color: #ececec;
            height: 300px;
            padding: 30px;
        }
    </style>
    <script>function reload_captcha() {
            document.getElementById('captcha_img').setAttribute('src', 'Util/get_captcha.php?r=' + Math.random());
        }</script>


<?php
if (isset($reset->message['verify']) && $reset->message['verify'] != "") {
    echo '<script>alert("' . $reset->message['verify'] . '")</script>';

}
if (isset($reset->message['reset']) && $reset->message['reset'] != "") {
    echo '<script>alert("' . $reset->message['reset'] . '")</script>';
}
?>


</head>


<?php
if ($reset->verified) {
?>

<body>
<div id="main">
    <form action="reset_pwd.php" method="post">
        <input type="text" name="email" hidden="hidden"
               value="<?php echo $_SESSION['email']; ?>">
        <table cellpadding="10px">
            <caption style="text-align: center">Reset password</caption>
            <tr>
                <td><label for="pwd">New password</label></td>
                <td><input type="password" id="pwd" name="pwd" required></td>
            </tr>
            <tr>
                <td><label for="pwd_repeat">password repeat</label></td>
                <td><input type="password" id="pwd_repeat" required></td>
                </td>
                <?php
                if (isset($reset->errors['pwd']))
                    echo '<td><span class="error"> ' . $reset->errors['pwd'] . '</span></td>'
                ?>
            </tr>
            <tr>
                <td colspan="3" align="center"><input class="button" type="submit" name="reset" value="ok"></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>


<?php

} else {
    ?>

    <div id="main">
        <form action="reset_pwd.php" method="post">
            <table cellpadding="10px">
                <caption>Reset password</caption>
                <tr>
                    <td><label for="email">Email</label></td>
                    <td><input type="email" name="email" required></td>
                    <span class="error"><?php
                        if (isset($reset->errors['email']))
                            echo $reset->errors['email'];
                        ?></span>
                </tr>
                <tr>
                    <td><label for="captcha">Captcha</label></td>
                    <td><input type="text" name="captcha" required></td>
                    <td><img src="Util/get_captcha.php" alt="captcha"
                             onclick="reload_captcha()" id="captcha_img"></td>
                </tr>
                <tr>
                    <td colspan="4" align="center">
                        <input class="button" type="submit" name="require_reset" value="Submit">
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>


        </form>
    </div>

    <?php
}
?>
