<?php

use Phalcon\Mvc\Model;

class Admins extends Model
{
    public $admin_id;
    public $username;
    public $password;

    /**
     * getAdmin($username, $password)
     *
     *function to check and return admin if exists
     *
     * @param [type] $username
     * @param [type] $password
     * @return void
     */
    public function getAdmin($username, $password)
    {
        return Admins::findFirst("username = '$username' AND password = '$password'");
    }
}
