<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;


class UserController extends Controller
{
    public function indexAction()
    {
        $this->view->users = Users::find();
        $this->view->locale = $this->getlocale;
    }
    public function addUserAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $roles = Roles::find();

        //caching the locale
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
            $user = new Users();

            $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';
            $payload = array(
                "iss" => "localhost:8080",
                "aud" => "localhost:8080",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                'role' => $role
            );

            $jwt = JWT::encode($payload, $key, 'HS256');


            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->role = $jwt;
            $result = $user->save();

            if ($result) {
                $this->view->tokenCheck = 1;
                $this->view->token = $jwt;
            }
        }
    }

    public function deleteAction()
    {
        $id = $this->request->get('id');
        $user = Users::find("user_id='$id'");
        $result = $user->delete();
        if ($result) {
            $this->response->redirect('/user');
        }
    }
}
