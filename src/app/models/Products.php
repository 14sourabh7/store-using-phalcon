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

    /**
     * getProducts()
     * function to get all products from db
     *
     * @return void
     */
    public function getProducts()
    {
        return Products::find();
    }


    /**
     * addProduct($productArr)
     * 
     * function to add product 
     *
     * @param [type] $productArr
     * @return void
     */
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

    /**
     * getLastProduct
     * 
     * function to get last added product
     *
     * @return void
     */
    public function getLastProduct()
    {
        return
            Products::findFirst(['order' => 'product_id DESC']);
    }
}
