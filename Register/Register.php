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
        $this->errors = array();

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

        if (isset($email) && isset($pwd) && isset($pwd_repeat) && isset($captcha)) {
            if ($captcha != $_SESSION['captcha']) {
                $this->errors['captcha'] = ERROR_ERR_CAPTCHA;
                return false;
            }

            if (!DBConnection::isUserExists($email)) {
                $this->errors['email'] = ERROR_NO_USER_FOUND;
                return false;
            }
            $hash = hash("sha256", $email . SECRET_KEY);
            DBConnection::updatePwdResetHash($email, $hash);
            //send mail
            include_once '../Util/send_email.php';
            send_email($email, '/register.php?active&email=' . $email . '&hash=' . $hash);
            $this->message['email'] = EMAIL_HAS_SENT;
            $this->email_sent = true;
            DBConnection::closeDBConnection();
        }
    }
}