<?php

use Phalcon\Mvc\Controller;


class SettingsController extends Controller
{
    public function indexAction()
    {

        //caching the locale
        $this->view->locale = $this->getlocale;

        $escaper = new \App\Components\MyEscaper();
        $settings = new Settings();
        $setting = $settings->getSettings();
        $bearer = $escaper->sanitize($this->request->get('bearer'));
        $locale = $escaper->sanitize($this->request->get('locale'));
        //variables to populate setting form
        $this->view->price = $setting->price;
        $this->view->stock = $setting->stock;
        $this->view->zip = $setting->zipcode;
        $this->view->title = $setting->title;
        $this->view->errorMessage = "";

        //checking post
        $check = $this->request->isPost();
        if ($check) {
            $inputs = $this->request->getPost();
            $title = $escaper->sanitize($inputs['title']);
            $price = $escaper->sanitize($inputs['price']);
            $stock = $escaper->sanitize($inputs['stock']);
            $zipcode = $escaper->sanitize($inputs['zip']);

            if ($title && $price && $stock && $zipcode) {

                //validating numeric input
                if (is_numeric($price) && is_numeric($stock) && is_numeric($zipcode)) {
                    $settingArr = [
                        'title' => $title,
                        'price' => $price,
                        'stock' => $stock,
                        'zipcode' => $zipcode
                    ];

                    //updating setting
                    $success = $setting->updateSetting($settingArr);

                    if ($success) {
                        $this->response->redirect('/product?bearer=' . $bearer . "&locale=" . $locale);
                    }
                } else {
                    $this->view->errorMessage = '*price, stock and zip must be numeric';
                }
            } else {
                $this->view->errorMessage = '*please fill all fields';
            }
        }
    }
}
