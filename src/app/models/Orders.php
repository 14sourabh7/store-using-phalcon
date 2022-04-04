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

    public function getOrders()
    {
        return Orders::find();
    }
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
}
