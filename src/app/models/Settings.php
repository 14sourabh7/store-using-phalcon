<?php

use Phalcon\Mvc\Model;

class Settings extends Model
{
    public $admin_id;
    public $title;
    public $price;
    public $stock;
    public $zipcode;

    public function getSettings()
    {
        return
            Settings::findFirst('admin_id=1');
    }
}
