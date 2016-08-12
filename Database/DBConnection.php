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

    public static function getDBConnection(){
        try {
            if (DBConnection::$db_connection == null) {
                DBConnection::$db_connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DBNAME, DB_USER, DB_PWD);
                DBConnection::$db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return DBConnection::$db_connection;
        } catch (PDOException $e ) {
//            $errors[] = DB_CONNECT_ERROR;
            return false;
        }
    }


    public static function checkCookieToken($db_connection, $username, $token)
    {
        $result = self::getAllInfoByName($db_connection, $username);
        if ($token == $result->token) {
            return true;
        }

        return false;
    }

    public static function getAllInfoByName($db_connection, $user_name)
    {
        $stmt = $db_connection->prepare('select * from user where user_name = :user_name');
        $stmt->bindValue(":user_name", $user_name);
        $stmt->execute();
        $result = $stmt->fetchObject();
        return $result;
    }

    public static function updateCookieToken($db_connection, $token, $username)
    {
        $stmt = $db_connection->prepare('update user set user_rememberme_token = :token where user_name = :username');
        $stmt->bindValue(":token", $token, PDO::PARAM_STR);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

    }

}