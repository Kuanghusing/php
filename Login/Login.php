<?php

/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午6:34
 */
class Login
{

    private $user_id;
    private $username;
    private $email;
    private $has_login_in = false;
    private $db_connection;

    public $errors = array("important" => "", "email" => "", "pwd" => "", "pwd_repeat" => "", "captcha" => "",);


    public function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            $this->loginWithSession($_SESSION['user_name'], $_SESSION['user_email']);

        }
        if (isset($_COOKIE['remember_me'])) {
            $this->loginWithCookies($_COOKIE['remember_me']);
        }


        $filter = array(
            "email" => FILTER_SANITIZE_EMAIL,
            "password" => FILTER_SANITIZE_STRING,
            "password_repeat" => FILTER_SANITIZE_STRING,
            "captcha" => FILTER_SANITIZE_STRING,
            "remember_me" => array("filter" => FILTER_VALIDATE_INT, "options" => array("min_range" => 0, "max_range" => 1,))

        );

        $filter_result = filter_input_array(INPUT_POST, $filter);
        if (isset($filter_result['email']))
            $this->loginWithPostData($filter_result['email'], $filter_result['password'], $filter_result['password_repeat'],
                $filter_result['captcha'], $filter_result['remember_me']);
    }

    private function loginWithSession($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->has_login_in = true;
    }

    private function loginWithCookies($cookie)
    {
        list($username, $token, $hash) = explode("|", $cookie);
        //check the cookie is valid
        if ($hash == hash("sha256", $username . $token . SECRET_KEY)) {
            //check token
            if ($this->db_connection = DBConnection::getDBConnection()) {
                if (DBConnection::checkCookieToken($this->db_connection, $username, $token)) {

                    $this->username = $username;
                    $result = DBConnection::getAllInfoByName($this->db_connection, $username);
                    $this->email = $result->user_email;
                    $this->user_id = $result->user_id;

                    $this->has_login_in = true;
                    $this->buildSession();
                    //rebuild cookie(token only use once)
                    $this->buildCookie();
                } else {
                    $this->clearCookie();
                }

                $this->db_connection = null;
            } else {
                $this->errors["important"] = DB_CONNECT_ERROR;
            }

        } else {
            $this->clearCookie();
        }
    }


    private function loginWithPostData($username, $pwd, $pwd_repeat, $captcha, $remember_me)
    {
        if (!isset($captcha) || $captcha != $_SESSION['captcha']) {
            $this->errors['captcha'] = ERROR_ERR_CAPTCHA;
            return false;
        }
        if (!isset($pwd) || !isset($pwd_repeat) || $pwd != $pwd_repeat) {
            $this->errors["pwd_repeat"] = ERROR_PWD_NOT_SAME;
            return false;

        }
        //user exists?
        $this->db_connection = DBConnection::getDBConnection();
        $result = DBConnection::getAllInfoByName($this->db_connection, $username);
        if (!isset($result->user_id)) {
            $this->errors['email'] = ERROR_NO_USER_FOUND;
            return false;
        }

        if (!password_verify($pwd, $result->password_hash)) {
            $this->errors['important'] = ERROR_LOGIN_ERROR;
            return false;
        }

        $this->user_id = $result->user_id;
        $this->username = $result->user_name;
        $this->email = $result->email;
        if ($remember_me === 0) {
            $this->clearCookie();
        } else {
            $this->buildCookie();
        }
        $this->buildSession();
        $this->has_login_in = true;
        $this->errors = array();
        $this->db_connection = null;

        return true;

    }


    private function buildSession()
    {
        $_SESSION['user_id'] = $this->user_id;
        $_SESSION['user_name'] = $this->username;
        $_SESSION['user_email'] = $this->email;
    }

    private function buildCookie()
    {
        $token = hash("sha256", mt_rand());
        $hash = hash("sha256", $this->username . $token . SECRET_KEY);
        $remember_me_string = $this->username . '|' . $token . '|' . $hash;
        setcookie("remember_me", $remember_me_string, time() + COOKIE_EXPIRE_TIME);

        //update token
        if ($this->db_connection = DBConnection::getDBConnection()) {
            DBConnection::updateCookieToken($this->db_connection, $token, $this->username);
        } else {
            $this->errors['important'] = DB_CONNECT_ERROR;
        }

        //TODO close db connect?
        $this->db_connection = null;

    }

    private function clearCookie()
    {
        setcookie("remember_me", "", time() - 60 * 60 * 24 * 365);
        if ($this->db_connection = DBConnection::getDBConnection()) {
            DBConnection::updateCookieToken($this->db_connection, "", $this->username);
        } else {
            $this->errors['important'] = DB_CONNECT_ERROR;
        }

        $this->db_connection = null;

    }

    public function userHasLoginIn()
    {
        return $this->has_login_in;
    }

}