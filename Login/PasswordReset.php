<?php

include_once '../Util/send_email.php';

class Reset
{

    /**
     * Reset constructor.
     */



    public $message = array("reset" => "", "verify" => "");

    public $verified = false;

    public function __construct()
    {


        if (isset($_POST['email']))
            $this->requestResetPwd();

        if (isset($_GET['email']))
            $this->verify();
    }

    private function requestResetPwd()
    {
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

        $captcha = filter_input(INPUT_POST, "captcha", FILTER_SANITIZE_STRING);

        if (!isset($email)) {
            $this->message['reset'] = ERROR_NOT_A_EMAIL;
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

        $hash = hash("sha256", $email.SECRET_KEY);
        DBConnection::updatePwdResetHash($email, $hash);

        if (send_email($email, $hash))
            $this->message['reset'] = EMAIL_HAS_SENT;

        DBConnection::closeDBConnection();
    }

    private function verify()
    {
        $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
        $hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);

        if (!isset($email)) {
            $this->message['verify'] = EMAIL_VERIFY_FAIL;
            return false;
        }

        if (DBConnection::checkTokenOrHash($email, $hash, PWD_RESET_HASH))
            $this->message['verify'] = EMAIL_VERIFY_OK;

        DBConnection::closeDBConnection();
    }

}