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



//secret key
define('SECRET_KEY', '$$96300kuahusg$$');

//cookie
define('COOKIE_EXPIRE_TIME', 60 * 60 * 24 * 7);


//errors
define('DB_CONNECT_ERROR', 'sorry, database connect error!');
define('ERROR_PWD_NOT_SAME', 'two password isn\'t the same!');
define('ERROR_ERR_CAPTCHA', 'wrong captcha');
define('ERROR_NO_USER_FOUND', 'user hasn\'t register');
define('ERROR_LOGIN_ERROR', 'login fail!');
define('ERROR_NOT_A_EMAIL', 'not a email!');
define('ERROR_USER_NOT_ACTIVE', 'sorry, you having active your account');
define('ERROR_LOGIN_FAIL_THREE_TIME', 'sorry, you had failed in logining for 3 three times, please wait for one hour and try it again...');




//mail
define('SERVER_HOST', 'http://127.0.0.1');
define('EMAIL_HOST', 'host');
define('EMAIL_USER_NAME', 'username');
define('EMAIL_PWD', 'pwd');
define('EMAIL_PORT', '587');
define('EMAIL_FROM', 'no_reply@xx.com');
define('EMAIL_FROM_NAME', 'my');
define('EMAIL_SUBJECT', 'Please verify your account');
define('EMAIL_BODY', 'click the link to verify ');
define('EMAIL_VERIFY_LINK', SERVER_HOST);
define('EMAIL_HAS_SENT', 'verify email has sent!');
define('EMAIL_VERIFY_FAIL', 'verify fail!');
define('EMAIL_VERIFY_OK', 'your account is active now!');
define('EMAIL_VERIFY_OUT_OF_TIME', 'fail, you havn\'t verify in and hour');



//token hash?
define('COOKIE_TOKEN', 1);
define('PWD_RESET_HASH', 2);
define('REGISTER_TOKEN', 3);