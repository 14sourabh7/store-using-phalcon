<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function deleteUser($user_id)
    {
        $user = Users::find("user_id='$user_id'");
        $result = $user->delete();
        return $result;
    }
}
