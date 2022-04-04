<?php

use Phalcon\Mvc\Model;

class Permissions extends Model
{
    public $id;
    public $component;
    public $action;
    public function getPermissions()
    {
        return Permissions::find(['order' => 'role']);
    }
}
