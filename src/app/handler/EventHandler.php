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

        $setting = Settings::findFirst('admin_id=1');
        $product = Products::findFirst(['order' => 'product_id DESC']);

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
        $setting = Settings::findFirst('admin_id=1');
        $order = Orders::findFirst(['order' => 'order_id DESC']);

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

        if ($role) {
            if (true === is_file($aclFile)) {
                $acl = unserialize(file_get_contents($aclFile));
                try {
                    $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';
                    $decoded = JWT::decode($role, new Key($key, 'HS256'));
                    $decodeArr = (array)$decoded;
                    $role = $decodeArr['role'];

                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                        $logger->log('unauthorised access', 'error');
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
            $admin = $this->session->get('admin');
            if (!$admin) {
                if ($controller !== 'admin' || $action !== 'index') {
                    $logger->log('unauthorised access', 'error');
                    die($local->_('authorised'));
                }
            }
        }
    }
}
