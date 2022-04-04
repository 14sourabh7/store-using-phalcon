<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        $this->response->redirect('/admin?bearer=&locale=en');
    }
}
