<?php

use Phalcon\Mvc\Model;

class Admins extends Model
{
    public $admin_id;
    public $username;
    public $password;
    public function getAdmin($username, $password)
    {
        return Admins::findFirst("username = '$username' AND password = '$password'");
    }
}
