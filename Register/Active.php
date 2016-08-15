<?php

class Active{
    public $has_active = false;


    public function __construct()
    {
        $this->justCheckSomething();
    }


    private function justCheckSomething()
        //...
    {
        $filter = array(
            "email" => FILTER_VALIDATE_EMAIL,
            "hash" => FILTER_SANITIZE_STRING
        );
        $filter_result = filter_input_array(INPUT_GET, $filter);

        $email = $filter_result['email'];
        $hash = $filter_result['hash'];
        if (!isset($email) || !isset($hash)){
            return false;
        }

        if (DBConnection::isUserExists($email) && DBConnection::isUserActive($email))
            return true;

        if (!DBConnection::checkTokenOrHash($email, $hash, REGISTER_TOKEN)) {
            $this->has_active = false;
            return false;
        }

        DBConnection::changeUserActiveState($email, true);
        $this->has_active = true;
        return true;
    }
}