<?php

/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午6:34
 */
class Login
{

    public $errors = array("important" => "", "email" => "", "pwd" => "", "pwd_repeat" => "", "captcha" => "",);
    private $user_id;
    private $username;
    private $email;
    private $has_login_in = false;
    private $db_connection;

    public function __construct()
    {
        $this->errors = array();

        if (isset($_GET['logout'])) {
            $this->clearCookie();
            echo '<script>alert("logout");</script>';
            $has_login_in = false;
            $this->errors = array();
            return;
        }


        if (isset($_SESSION['user_id']))
            $this->loginWithSession($_SESSION['user_name'], $_SESSION['user_email']);

        if (isset($_COOKIE['remember_me']))
            $this->loginWithCookies($_COOKIE['remember_me']);

        if (isset($_POST['login']))
            $this->loginWithPostData();


    }

    private function clearCookie()
    {
        setcookie("remember_me", "", time() - 60 * 60 * 24 * 365);
        DBConnection::updateToken(null, $this->username, COOKIE_TOKEN);
        $this->errors = array();
        $this->db_connection = null;

    }

    private function loginWithSession($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->has_login_in = true;
        $this->errors = array();
        $this->buildSession();

    }

    private function buildSession()
    {
        session_start();
        $_SESSION['user_id'] = $this->user_id;
        $_SESSION['user_name'] = $this->username;
        $_SESSION['user_email'] = $this->email;
    }

    private function loginWithCookies($cookie)
    {
        list($username, $token, $hash) = explode("|", $cookie);
        //check the cookie is valid
        if ($hash == hash("sha256", $username . $token . SECRET_KEY)) {
            //check token
            if (DBConnection::checkTokenOrHash($username, $token, COOKIE_TOKEN)) {

                $this->username = $username;
                $result = DBConnection::getAllInfoByName($username);
                $this->email = $result->user_email;
                $this->user_id = $result->user_id;

                $this->has_login_in = true;
                $this->buildSession();
                //rebuild cookie(token only use once)
                $this->buildCookie();
            } else {
                $this->clearCookie();
                $this->has_login_in = false;
            }

            $this->db_connection = null;
            $this->errors = array();
        } else {
            $this->clearCookie();
        }
        DBConnection::closeDBConnection();
    }

    private function buildCookie()
    {
        $token = hash("sha256", mt_rand());
        $hash = hash("sha256", $this->username . $token . SECRET_KEY);
        $remember_me_string = $this->username . '|' . $token . '|' . $hash;
        setcookie("remember_me", $remember_me_string, time() + COOKIE_EXPIRE_TIME);

        //update token
        DBConnection::updateToken($token, $this->username, COOKIE_TOKEN);

        //TODO close db connect?
        $this->db_connection = null;

    }

    private function loginWithPostData()
    {
        $filter = array(
            "email" => FILTER_VALIDATE_EMAIL,
            "password" => FILTER_SANITIZE_STRING,
            "captcha" => FILTER_SANITIZE_STRING,

        );

        $filter_result = filter_input_array(INPUT_POST, $filter);

        $email = $filter_result['email'];
        $pwd = $filter_result['password'];
        $captcha = $filter_result['captcha'];
        //not a email
        if (!$email) {
            $this->errors['email'] = ERROR_NOT_A_EMAIL;
            return false;
        }
        //it can't be true
        if (!isset($pwd) || !isset($captcha)) {
            $this->errors['important'] = ERROR_LOGIN_ERROR;
        }
        if (!isset($captcha) || $captcha != $_SESSION['captcha']) {
            $this->errors['captcha'] = ERROR_ERR_CAPTCHA;
            return false;
        }
        /*if (!isset($pwd) || !isset($pwd_repeat) || $pwd != $pwd_repeat) {
            $this->errors["pwd_repeat"] = ERROR_PWD_NOT_SAME;
            return false;

        }*/
        //user exists?
        $result = DBConnection::getAllInfoByName($email);
        if (!isset($result->user_id)) {
            $this->errors['email'] = ERROR_NO_USER_FOUND;
            return false;
        }


        if (DBConnection::isUserLoginFailThreeTimes($email))
        {
            $this->errors['important'] = ERROR_LOGIN_FAIL_THREE_TIME;
            return false;
        }

        if (!password_verify($pwd, $result->user_password_hash)) {
            $this->errors['important'] = ERROR_LOGIN_ERROR;
            DBConnection::userLoginFail($email);
            print_r(ERROR_LOGIN_ERROR);
            return false;
        }

        if (!DBConnection::isUserActive($email)) {
            $this->errors['important'] = ERROR_USER_NOT_ACTIVE;
            return false;
        }
        $this->user_id = $result->user_id;
        $this->username = $result->user_name;
        $this->email = $result->email;


        isset($_POST['remember_me']) ? $this->buildCookie() : $this->clearCookie();
        $this->buildSession();
        $this->has_login_in = true;
        $this->errors = array();
        $this->db_connection = null;
        DBConnection::closeDBConnection();
        return true;

    }

    public function userHasLoginIn()
    {
        return $this->has_login_in;
    }

}
