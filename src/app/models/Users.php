<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;


    public function getUsers()
    {
        return Users::find();
    }


    /**
     * addUser($name, $email, $password, $role)
     * 
     * function to add user
     *
     * @param [type] $name
     * @param [type] $email
     * @param [type] $password
     * @param [type] $role
     * @return void
     */
    public function addUser($name, $email, $password, $role)
    {
        $user = new Users();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;
        $result = $user->save();
        return $result;
    }


    /**
     * deleteUser($user_id)
     * 
     * function to delete a user
     *
     * @param [type] $user_id
     * @return void
     */
    public function deleteUser($user_id)
    {
        $user = Users::find("user_id='$user_id'");
        $result = $user->delete();
        return $result;
    }
}
