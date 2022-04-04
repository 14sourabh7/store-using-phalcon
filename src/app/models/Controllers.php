<?php

use Phalcon\Mvc\Model;

class Controllers extends Model
{
    public $controller;
    public function getControllers()
    {
        return Controllers::find();
    }
}
