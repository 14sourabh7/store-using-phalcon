<?php

use Phalcon\Mvc\Model;

class Orders extends Model
{
    public $order_id;
    public $name;
    public $address;
    public $zip;
    public $product;
    public $quantity;
    public $date;

    /**
     * getOrders()
     * 
     * function to return all orders
     *
     * @return void
     */
    public function getOrders()
    {
        return Orders::find();
    }

    /**
     * addOrder($orderArr)
     * 
     * function to add a new order
     *
     * @param [type] $orderArr
     * @return void
     */
    public function addOrder($orderArr)
    {
        $order = new Orders();
        $order->assign(
            $orderArr,
            [
                'name', 'address', 'zip', 'product', 'quantity', 'date'
            ]
        );
        $success = $order->save();

        if ($success) {

            //if order is placed updating products
            $id = $orderArr['product'];
            $product = Products::findFirst("product_id = '$id'");
            $quantity = $product->stock;
            $quantity = $quantity - $orderArr['quantity'];
            if ($quantity > 0) {
                $product->stock = $quantity;
                $product->save();
            } else {
                $product->delete();
            }
        }
        return $success;
    }
    public function getLastOrder()
    {
        return
            Orders::findFirst(['order' => 'order_id DESC']);
    }
}
