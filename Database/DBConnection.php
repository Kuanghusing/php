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

    public static function getAllInfoByName($user_name)
    {
        try {
            $stmt = self::getDBConnection()->prepare('SELECT * FROM user WHERE user_name = :user_name');
            $stmt->bindValue(":user_name", $user_name);
            $stmt->execute();
            $result = $stmt->fetchObject();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $result;
    }

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
            echo $e;
            die();
        }
    }

    public static function changeUserActiveState($email, $active_state)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            try {
                $stmt = $db_connection->prepare('UPDATE user SET user_active = :active WHERE user_email = :email');
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                if ($active_state)
                    $stmt->bindValue(':active', 1, PDO::PARAM_INT);
                else
                    $stmt->bindValue(':active', 0, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    public static function isUserExists($email)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            try {
                $stmt = $db_connection->prepare('SELECT user_email FROM user WHERE user_email = :email');
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                // $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = $stmt->fetchObject();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
            $db_connection = null;
            return isset($result->user_email);
        }
    }

    public static function updateToken($token, $username, $type)
    {
        $db_connection = self::getDBConnection();
        try {
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
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        $db_connection = null;
    }


    public static function userLoginFail($username)
    {
        try {
            $result = self::getAllInfoByName($username);
            $fail_count = $result->user_failed_logins;
            $stmt = self::getDBConnection()->prepare('UPDATE user SET
            user_failed_logins=:count ,user_last_failed_login = :last WHERE user_name = :username');
            $stmt->bindValue(":count", $fail_count + 1, PDO::PARAM_INT);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->bindValue(":last", time(), PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
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
        var_dump($fail_count);
        var_dump($last_fail);
        var_dump(time());
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
        try {
            $stmt = self::getDBConnection()->prepare('UPDATE user SET user_password_reset_hash = :hash ,user_password_reset_timestamp = :time WHERE user_email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':time', date("Y-m-d h:i:s", strtotime("now")), PDO::PARAM_STR);
            $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public static function checkResetPwdTime($email)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            try {
                $stmt = $db_connection->prepare('SELECT user_password_reset_timestamp FROM user WHERE user_email = :email');
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch();
                $last_time = $result[0];
                //last_time : datetime not timestamp
                //TODO
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
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
                try {
                    $stmt = $db_connection->prepare('UPDATE user SET user_password_hash = :pwd , user_password_reset_hash = NULL WHERE user_email = :email ');
                    $stmt->bindValue(':pwd', password_hash($new_pwd, PASSWORD_DEFAULT), PDO::PARAM_STR);
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();
                    $db_connection = null;
                    return true;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                    die();
                }
            }
        }
    }

    public function insertNewUser($email, $pwd_hash, $active_hash)
    {
        $db_connection = self::getDBConnection();
        if ($db_connection instanceof PDO) {
            try {
                $stmt = $db_connection->prepare("insert into user (user_name, user_password_hash, user_email, user_active,
          user_activation_hash,  user_failed_logins, user_registration_datetime,
          user_registration_ip) values(:name, :pwd, :email,  DEFAULT, :active_hash, DEFAULT, DEFAULT, :ip);");
                $stmt->bindValue(':name', $email, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':pwd', $pwd_hash, PDO::PARAM_STR);
                $stmt->bindValue(':active_hash', $active_hash, PDO::PARAM_STR);
                $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
                $stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }



}
