<?php

/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午5:57
 */
class DBConnection
{

    private static $db_connection;

//    public $errors = array();

    private static function getDBConnection()
    {
        try {
            if (DBConnection::$db_connection == null) {
                DBConnection::$db_connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DBNAME, DB_USER, DB_PWD);
                DBConnection::$db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return DBConnection::$db_connection;
        } catch (PDOException $e) {
//            $errors[] = DB_CONNECT_ERROR;
            return false;
        }
    }


    public static function checkTokenOrHash($username, $token, $type)
    {
        $result = self::getAllInfoByName($username);
        switch ($type) {
            case COOKIE_TOKEN:
                return $token == $result->user_rememberme_token;
            case PWD_RESET_HASH:
                return $token == $result->user_password_reset_hash;
            case REGISTER_TOKEN:
                return $token == $result->user_activation_hash;
            default:
                return false;
        }


        return false;
    }

    public static function changeUserActiveState($email, $active_state)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            $stmt = $db_connection->prepare('UPDATE user SET user_active = :active WHERE user_email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            if ($active_state)
                $stmt->bindValue(':active', 1, PDO::PARAM_INT);
            else
                $stmt->bindValue(':active', 0, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public static function getAllInfoByName($user_name)
    {
        $stmt = self::getDBConnection()->prepare('SELECT * FROM user WHERE user_name = :user_name');
        $stmt->bindValue(":user_name", $user_name);
        $stmt->execute();
        $result = $stmt->fetchObject();
        return $result;
    }

    public static function isUserExists($email)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            $stmt = $db_connection->prepare('SELECT user_email FROM user WHERE user_email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $db_connection = null;
            return isset($result[0]);
        }
    }

    public static function updateToken($token, $username, $type)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            switch ($type) {
                case COOKIE_TOKEN:
                    $stmt = $db_connection->prepare('UPDATE user SET user_rememberme_token = :token WHERE user_name = :username');
                    break;
                case REGISTER_TOKEN:
                    $stmt = $db_connection->prepare('UPDATE user SET user_activation_hash = :token WHERE user_name = :username');
                    break;
                default:
                    return;
            }
            if ($token == null) {
                $token = "NULL";
            }
            $stmt->bindValue(":token", $token, PDO::PARAM_STR);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $db_connection = null;
        }
    }


    public static function userLoginFail($username)
    {
        $result = self::getAllInfoByName($username);
        $fail_count = $result->user_failed_logins;
        $stmt = self::getDBConnection()->prepare('UPDATE user SET user_failed_logins=:count WHERE user_name = :username');
        $stmt->bindValue(":count", $fail_count + 1, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function isUserActive($username)
    {
        $result = self::getAllInfoByName($username);
        $active = $result->user_active;
        return $active == 0 ? false : true;
    }

    public static function isUserLoginFailThreeTimes($username)
    {
        $result = self::getAllInfoByName($username);
        $last_fail = $result->user_last_failed_login;
        $fail_count = $result->user_failed_logins;
        if ($fail_count >= 3) {
            //one hour
            if ($last_fail + 60 * 60 > time()) {
                return true;
            }
        }
    }

    public static function closeDBConnection()
    {
        if (self::$db_connection != null) {
            self::$db_connection = null;
        }
    }


    public static function updatePwdResetHash($email, $hash)
    {
        $stmt = self::getDBConnection()->prepare('UPDATE user SET user_password_reset_hash = :hash ,user_password_reset_timestamp = :time WHERE user_email = :email');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':time', time(), PDO::PARAM_INT);
        $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
        $stmt->execute();
    }

    public static function checkResetPwdTime($email)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            $stmt = $db_connection->prepare('SELECT user_password_reset_timestamp FROM user WHERE user_email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            $last_time = $result[0];
            $db_connection = null;
            if (isset($last_time)) {
                if ($last_time + 60 * 60 > time()) {
                    return false;
                } else
                    return true;
            }
            return true;

        }
    }

    public static function resetPwd($email, $old_pwd = null, $new_pwd)
    {
        if ($old_pwd != null) {
            //change pwd
            //TODO
        } else {
            //reset pwd
            $db_connection = self::getDBConnection();
            if ($db_connection instanceof PDO) {
                $stmt = $db_connection->prepare('UPDATE user SET user_password_hash = :pwd , user_password_reset_hash = NULL WHERE user_email = :email ');
                $stmt->bindValue(':pwd', password_hash($new_pwd, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $db_connection = null;
                return true;
            }

        }
    }



}