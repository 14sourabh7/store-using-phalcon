<?php

use Phalcon\Mvc\Model;

class Products extends Model
{
    public $product_id;
    public $name;
    public $description;
    public $tags;
    public $price;
    public $stock;

    public function getProducts()
    {
        return Products::find();
    }

    public function addProduct($productArr)
    {
        $product = new Products();
        $product->assign(
            $productArr,
            [
                'name', 'description', 'tags', 'price', 'stock'
            ]
        );
        $success = $product->save();
        return $success;
    }
}
