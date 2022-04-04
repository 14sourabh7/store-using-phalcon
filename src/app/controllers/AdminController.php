<?php

use Phalcon\Mvc\Controller;

class AdminController extends Controller
{


    public function indexAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $this->view->message = '';
        $logger = new \App\Components\MyLogger();

        //checking post request
        $check = $this->request->isPost();
        if ($check) {
            if ($this->request->getPost()['username'] && $this->request->getPost()['password']) {

                $username =
                    $escaper->sanitize($this->request->getPost()['username']);
                $password =
                    $escaper->sanitize($this->request->getPost()['password']);

                $admin = new Admins();
                $data = $admin->getAdmin($username, $password);


                if ($data) {
                    //if credentials are correct 
                    $this->session->set('admin', 1);
                    $this->response->redirect("/product?bearer=&locale=en");
                } else {
                    $message = 'authentication failed';
                    $logger->log($message, 'error');
                    $this->view->message = $message;
                }
            } else {
                $this->view->message = $this->locale->_('please fill all fields');
            }
        }
    }

    public function logoutAction()
    {
        $this->session->set('admin', 0);
        $this->response->redirect("/admin?bearer=&locale=en");
    }
}
