<?php

class Register
{
    public $email_sent = false;

    public $errors = array('important' => '', 'email' => '', 'pwd' => '', 'pwd_repeat' => '', 'captcha' => '');

    public $message = array();

    public function __construct()
    {
        if (isset($_POST['email']))
            $this->registerWithPostData();
    }

    private function registerWithPostData()
    {

        $filter = array(
            "email" => FILTER_VALIDATE_EMAIL,
            "pwd" => FILTER_SANITIZE_STRING,
            "pwd_repeat" => FILTER_SANITIZE_STRING,
            "captcha" => FILTER_SANITIZE_STRING
        );

        $filter_result = filter_input_array(INPUT_POST, $filter);

        $email = $filter_result['email'];
        $pwd = $filter_result['pwd'];
        $pwd_repeat = $filter_result['pwd_repeat'];
        $captcha = $filter_result['captcha'];

        print_r("register with post<br />");
        if (isset($email) && isset($pwd) && isset($pwd_repeat) && isset($captcha)) {
            if ($captcha != $_SESSION['captcha']) {
                $this->errors['captcha'] = ERROR_ERR_CAPTCHA;
                print_r(ERROR_ERR_CAPTCHA . "<br />");
                return false;

            }

            if ($pwd != $pwd_repeat) {
                $this->errors['pwd_repeat'] = ERROR_PWD_NOT_SAME;
                print_r(ERROR_PWD_NOT_SAME);
                return false;
            }
            if (DBConnection::isUserExists($email)) {
                $this->errors['email'] = ERROR_USER_EXISTS;
                print_r(ERROR_USER_EXISTS);
                return false;
            }
            // $hash = hash("sha256", $email . SECRET_KEY);
            $hash = sha1(mt_rand());
            DBConnection::insertNewUser($email, password_hash($pwd, PASSWORD_DEFAULT), $hash);

            // DBConnection::updatePwdResetHash($email, $hash);
            //send mail
            print_r("can you see me?");
            include_once '../Util/send_email.php';
            send_email($email, '/register.php?active&email=' . $email . '&hash=' . $hash);
            $this->message['email'] = EMAIL_HAS_SENT;
            $this->email_sent = true;
            DBConnection::closeDBConnection();
        }
    }
}
