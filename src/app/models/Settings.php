<?php

use Phalcon\Mvc\Model;

class Settings extends Model
{
    public $admin_id;
    public $title;
    public $price;
    public $stock;
    public $zipcode;

    /**
     * getSettings()
     * 
     * function to get the settings
     *
     * @return void
     */
    public function getSettings()
    {
        return
            Settings::findFirst('admin_id=1');
    }

    /**
     * updateSettings($settingArr)
     * 
     * function to update the settings
     *
     * @param [type] $settingArr
     * @return void
     */
    public function updateSetting($settingArr)
    {
        $setting = Settings::findFirst('admin_id=1');
        $setting->assign(
            $settingArr,
            [
                'title', 'price', 'stock', 'zipcode'
            ]
        );
        $success = $setting->update();
        return $success;
    }
}
