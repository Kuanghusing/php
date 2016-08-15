<?php

include_once '../Util/send_email.php';

class Reset
{

    /**
     * Reset constructor.
     */


    public $message = array("reset" => "", "verify" => "");

    public $errors = array("pwd" => "", "email" => "");

    public $verified = false;

    public $has_reset_pwd = false;
    public function __construct()
    {


        if (isset($_POST['require_reset']))
            $this->requestResetPwd();

        if (isset($_GET['email']))
            $this->verify();

        if (isset($_POST['reset']))
            $this->resetPwd();

    }

    private function requestResetPwd()
    {
        $this->message['verify'] = null;
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

        $captcha = filter_input(INPUT_POST, "captcha", FILTER_SANITIZE_STRING);

        if (!isset($email)) {
            $this->errors['email'] = ERROR_NOT_A_EMAIL;
            return false;
        }

        if ($captcha != $_SESSION['captcha']) {
            $this->message['reset'] = ERROR_ERR_CAPTCHA;
            return false;
        }

        //user exists?
        $result = DBConnection::getAllInfoByName($email);
        if (!isset($result->user_id)) {
            $this->message['reset'] = ERROR_NO_USER_FOUND;
            return false;
        }

        $hash = hash("sha256", $email . SECRET_KEY);
        DBConnection::updatePwdResetHash($email, $hash);

        if (send_email($email, '/reset_pwd.php?email='.$email.'&hash='.$hash))
            $this->message['reset'] = EMAIL_HAS_SENT;

        DBConnection::closeDBConnection();
    }

    private function verify()
    {
        $this->message['reset'] = null;
        $this->verified = false;
        $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
        $hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);

        if (!isset($email)) {
            $this->message['verify'] = EMAIL_VERIFY_FAIL;
            return false;
        }

        if (!DBConnection::checkTokenOrHash($email, $hash, PWD_RESET_HASH)) {
            $this->message['verify'] = EMAIL_VERIFY_FAIL;
            return false;
        }
        if (!DBConnection::checkResetPwdTime($email)) {
            $this->message['verify'] = EMAIL_VERIFY_OUT_OF_TIME;
            return false;

        }

        $this->verified = true;


        DBConnection::closeDBConnection();
    }

    private function resetPwd()
    {
        $this->has_reset_pwd = false;
        $pwd = filter_input(INPUT_POST, "pwd", FILTER_SANITIZE_STRING);

        $pwd_repeat = filter_input(INPUT_POST, "pwd_repeat", FILTER_SANITIZE_STRING);

        if ($pwd !== $pwd_repeat) {
            $this->errors['pwd'] = ERROR_PWD_NOT_SAME;
            return false;
        }
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
        if (DBConnection::resetPwd($email, null, $pwd)) {
            $this->has_reset_pwd = true;
        }


        DBConnection::closeDBConnection();
    }

}