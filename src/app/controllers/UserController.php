<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;


class UserController extends Controller
{
    public function indexAction()
    {
        $users = new Users();
        $this->view->users = $users->getUsers();
        $this->view->locale = $this->getlocale;
    }

    /**
     * action to add user and generate token
     *
     * @return void
     */
    public function addUserAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $roles = new Roles();
        $roles = $roles->getRoles();


        $this->view->locale = $this->getlocale;
        $this->view->roles = $roles;
        $this->view->tokenCheck = 0;
        $this->view->msg = "";

        $check = $this->request->isPost();
        if ($check) {
            $inputs = $this->request->getPost();
            $name = $escaper->sanitize($inputs['name']);
            $email = $escaper->sanitize($inputs['email']);
            $password = $escaper->sanitize($inputs['password']);
            $role = $escaper->sanitize($inputs['roles']);

            $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';
            $payload = array(
                "iss" => "localhost:8080",
                "aud" => "localhost:8080",
                'role' => $role
            );

            $jwt = JWT::encode($payload, $key, 'HS256');

            $user = new Users();
            $result = $user->addUser($name, $email, $password, $jwt);

            if ($result) {
                $this->view->tokenCheck = 1;
                $this->view->token = $jwt;
                if ($role == 'manager') {
                    $page = 'product';
                } else {
                    $page = 'order';
                }
                $this->view->page = $page;
            }
        }
    }


    /**
     * action to delete user
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $user = new Users();
        $result =  $user->deleteUser($id);
        if ($result) {
            $this->response->redirect('/user?bearer=&locale=en');
        }
    }
}
