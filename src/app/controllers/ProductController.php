<?php

use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        //caching the locale
        $this->view->locale = $this->getlocale;
        $product = new Products();
        $this->view->products = $product->getProducts();;
    }


    public function addAction()
    {

        $this->view->locale = $this->getlocale;

        $escaper = new \App\Components\MyEscaper();
        $product = new Products();

        $checkPost = $this->request->isPost();
        $this->view->errorMessage = "";
        $bearer = $escaper->sanitize($this->request->get('bearer'));
        $locale = $escaper->sanitize($this->request->get('locale'));
        if ($checkPost) {

            $inputs = $this->request->getPost();
            $name = $escaper->sanitize($inputs['name']);
            $description = $escaper->sanitize($inputs['description']);
            $tags = $escaper->sanitize($inputs['tags']);
            $price = $escaper->sanitize($inputs['price']);
            $stock = $escaper->sanitize($inputs['stock']);

            $productArr = [
                'name' => $name,
                'description' => $description,
                'tags' => $tags,
                'price' => $price,
                'stock' => $stock
            ];


            if ($name && $description && $tags) {

                if ($price || $stock) {
                    $checkP = 1;
                    $checkS = 1;
                    $price = 0;
                    $stock = 0;

                    if ($price) {
                        if (is_numeric($price)) {
                            $checkP = 1;
                        } else {
                            $checkP = 0;
                            $this->view->errorMessage = $this->locale->_('er4');
                        }
                    }

                    if ($stock) {

                        if (is_numeric($stock)) {
                            $checkS = 1;
                        } else {
                            $checkS = 0;
                            $this->view->errorMessage = $this->locale->_('er4');
                        }
                    }

                    if ($checkP && $checkS) {
                        $success = $product->addProduct($productArr);
                    }
                } else {
                    $productArr['price'] = 0;
                    $productArr['stock'] = 0;
                    $success = $product->addProduct($productArr);
                }

                if ($success) {
                    $eventManager = $this->di->get('EventsManager');
                    $eventManager->fire('order:productSave', $this);
                    $this->response->redirect("/product?bearer=" . $bearer . "&locale=" . $locale);
                }
            } else {
                $this->view->errorMessage = $this->locale->_('er5');
            }
        }
    }
}
