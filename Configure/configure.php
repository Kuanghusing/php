<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-12
 * Time: 下午5:59
 */

//database
define('DB_HOST', 'localhost');
define('DB_USER', 'kuahusg');
define('DB_PWD', '9630088659');
define('DB_DBNAME', 'user');

//db error
define('DB_CONNECT_ERROR', 'sorry, database connect error!');


//secret key
define('SECRET_KEY', '$$96300kuahusg$$');

//cookie
define('COOKIE_EXPIRE_TIME', 60 * 60 * 24 * 7);


//errors
define('ERROR_PWD_NOT_SAME', 'two password isn\'t the same!');
define('ERROR_ERR_CAPTCHA', 'wrong captcha');
define('ERROR_NO_USER_FOUND', 'user hasn\'t register');
define('ERROR_LOGIN_ERROR', 'login fail!');