<?php

use Phalcon\Mvc\Model;

class Controllers extends Model
{
    public $controller;

    /**
     * getControllers()
     * function to get all controllers from db
     *
     * @return void
     */
    public function getControllers()
    {
        return Controllers::find();
    }
}
