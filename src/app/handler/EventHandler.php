<?php
// event handle class
namespace App\Handler;

use Phalcon\Di\Injectable;
use Products;
use Orders;
use Settings;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EventHandler extends Injectable
{

    /**
     * productSave()
     *
     * this event function triggers once the product is saved
     * @param [type] $product
     * @param [type] $setting
     * @return void
     */
    public function productSave()
    {
        $logger = new \App\Components\MyLogger();
        $settings = new Settings();
        $products = new Products();
        $setting = $settings->getSettings();
        $product = $products->getLastProduct();

        if ($product->stock == 0) {
            $product->stock = $setting->stock;
        }

        if ($product->price == 0) {
            $product->price = $setting->price;
        }

        if ($setting->title == 'with') {
            $name = $product->name . " " . $product->tags;
            $product->name = $name;
        }

        $product->update();
        $logger->log("product updated", 'info');
    }

    /**
     * orderSave($order, $setting)
     * 
     * this event function triggers when the order is saved
     *
     * @param [type] $order
     * @param [type] $setting
     * @return void
     */
    public function orderSave()
    {
        $logger = new \App\Components\MyLogger();
        $setting = new Settings();
        $order = new Orders();
        $setting = $setting->getSettings();
        $order = $order->getLastOrder();

        if ($order->zip == 0) {
            $order->zip = $setting->zipcode;
        }

        $order->update();
        $logger->log("order updated", 'info');
    }


    /**
     * beforeHandleRequest()
     * 
     * function to check permissions
     *
     * @return void
     */
    public function beforeHandleRequest()
    {
        $application = new \Phalcon\Mvc\Application();
        $locale = new \App\Components\Locale();
        $logger = new \App\Components\MyLogger();

        $local = $locale->getTranslator();

        $aclFile = '../app/security/acl.cache';

        $role = $application->request->get('bearer');
        $controller
            = $application->router->getControllerName();
        $action
            = $application->router->getActionName() ? $application->router->getActionName() : 'index';
        $errorMsg = 'unauthorised access';

        //if bearer is given in url
        if ($role) {
            if (true === is_file($aclFile)) {
                $acl = unserialize(file_get_contents($aclFile));
                try {
                    $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';
                    $decoded = JWT::decode($role, new Key($key, 'HS256'));
                    $decodeArr = (array)$decoded;
                    $role = $decodeArr['role'];

                    //checking for permissions
                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                        $logger->log($errorMsg, 'error');
                        die($local->_('authorised'));
                    }
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    echo $message;
                    echo $local->_('access');
                    $logger->log($message, 'error');
                    die;
                }
            } else {
                die($local->_('filenot'));
            }
        } else {
            //if admin grant access everywhere
            $admin = $this->session->get('admin');

            //if not admin check whether page is admin / index or not!!
            if (!$admin) {
                if ($controller !== 'admin' || $action !== 'index') {
                    $logger->log($errorMsg, 'error');
                    die($local->_('authorised'));
                }
            }
        }
    }
}
