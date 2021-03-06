<?php

use Phalcon\Mvc\Controller;


class OrderController extends Controller
{
    public function indexAction()
    {
        $this->view->locale = $this->getlocale;
        $order = new Orders();
        $this->view->orders = $order->getOrders();
    }

    public function addAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $product = new Products();
        $order = new Orders();

        $date = getDate();
        $currentDate =  $date['year'] . "-" . $date['mon'] . "-" . $date['mday'];


        $this->view->locale = $this->getlocale;
        $this->view->products = $product->getProducts();
        $this->view->errorMessage = "";
        $bearer = $escaper->sanitize($this->request->get('bearer'));
        $locale = $escaper->sanitize($this->request->get('locale'));

        //checking post request
        $checkPost = $this->request->isPost();
        if ($checkPost) {

            $inputs = $this->request->getPost();
            $name = $escaper->sanitize($inputs['name']);
            $address = $escaper->sanitize($inputs['address']);
            $quantity = $escaper->sanitize($inputs['quantity']);
            $product = $escaper->sanitize($inputs['product']);
            $zip = $escaper->sanitize($inputs['zip']);
            $orderArr = [
                'name' => $name,
                'address' => $address,
                'zip' => $zip,
                'product' => $product,
                'quantity' => $quantity,
                'date' => $currentDate
            ];

            //checking for input
            if ($name && $address && $quantity && $product) {


                //validating quantity
                if (is_numeric($quantity)) {
                    if ($zip) {
                        //validating zip
                        if (is_numeric($zip)) {

                            $success = $order->addOrder($orderArr);
                        } else {
                            $this->view->errorMessage = $this->locale->_('er1');
                        }
                    } else {
                        $orderArr['zip'] = 0;
                        $success = $order->addOrder($orderArr);
                    }

                    //if order is added
                    if ($success) {

                        //firing event for after order save event
                        $eventManager = $this->di->get('EventsManager');
                        $eventManager->fire('order:orderSave', $this);
                        $this->response->redirect("/order?bearer=" . $bearer . "&locale=" . $locale);
                    }
                } else {
                    $this->view->errorMessage =
                        $this->locale->_('er2');
                }
            } else {
                $this->view->errorMessage
                    = $this->locale->_('er3');
            }
        }
    }
}
