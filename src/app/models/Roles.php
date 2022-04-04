<?php

use Phalcon\Mvc\Model;

class Roles extends Model
{
    public $role;

    /**
     * getRoles()
     * 
     * function to get roles from db
     *
     * @return void
     */
    public function getRoles()
    {
        return Roles::find();
    }
}
