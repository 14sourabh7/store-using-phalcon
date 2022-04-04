<?php

use Phalcon\Mvc\Model;

class Permissions extends Model
{
    public $id;
    public $component;
    public $action;

    /**
     * getPermissions()
     * function to get permissions from db 
     *
     * @return void
     */
    public function getPermissions()
    {
        return Permissions::find(['order' => 'role']);
    }
}
