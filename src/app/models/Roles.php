<?php

use Phalcon\Mvc\Model;

class Roles extends Model
{
    public $role;
    public function getRoles()
    {
        return Roles::find();
    }
}
