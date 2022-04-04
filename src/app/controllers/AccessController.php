<?php
//controller to handle access
use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;

class AccessController extends Controller
{
    public function indexAction()
    {
    }

    /**
     * buildaclAction
     * 
     * action to build acl file based on admin selections
     *
     * @return void
     */
    public function buildaclAction()
    {
        //caching the locale

        $this->view->locale = $this->getlocale;

        $permissions = new Permissions();
        $permission = $permissions->getPermissions();
        $roles = new Roles();
        $role = $roles->getRoles();
        $controllers = new Controllers();
        $controller = $controllers->getControllers();
        $this->view->permissions = $permission;
        $this->view->roles = $role;
        $this->view->controllers = $controller;

        $check = $this->request->getPost();
        if ($check) {
            $dbrole = $this->request->getPost()['roles'];
            $dbcontroller = $this->request->getPost()['controller'];
            $dbaction = $this->request->getPost()['action'];
            $permissionCheck = Permissions::find("role = '$dbrole' AND action= '$dbaction' AND controller='$dbcontroller'");

            if (count($permissionCheck) < 1) {
                $permissions->role = $dbrole;
                $permissions->controller = $dbcontroller;
                $permissions->action = $dbaction;
                $result = $permissions->save();
                if ($result) {
                    $aclFile = APP_PATH . '/security/acl.cache';
                    $acl = new Memory();

                    foreach ($role as $r) {
                        $acl->addRole($r->role);
                    }
                    foreach ($controller as $c) {
                        if ($c->controller == 'user') {
                            $acl->addComponent($c->controller, ['index', 'login', 'signup']);
                            continue;
                        }
                        $acl->addComponent($c->controller, ['index', 'add', 'update', 'delete']);
                    }
                    $permission = $permissions->getPermissions();
                    foreach ($permission as $p) {
                        $acl->allow($p->role, $p->controller, $p->action);
                    }
                    file_put_contents($aclFile, serialize($acl));
                    $this->response->redirect('/access/buildacl?role=admin');
                }
            }
        }
    }


    /**
     * addroleAction()
     * 
     * action to add roles manually by admin
     *
     * @return void
     */
    public function addroleAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $roles = new Roles();
        $this->view->roles = $roles->getRoles();
        $dbroles = $roles->getRoles();
        $check = $this->request->isPost();
        $checkRole = 0;
        $this->view->errorMsg = "";
        if ($check) {
            $inpRole = $escaper->sanitize($this->request->getPost('roles'));
            foreach ($dbroles as $r) {
                if ($inpRole == $r->role) {
                    $checkRole = 1;
                    break;
                }
            }
            if (!$checkRole) {
                $roles->role = $inpRole;
                $result = $roles->save();
                if ($result) {
                    $this->response->redirect('/access/addrole?role=admin');
                }
            } else {
                $this->view->errorMsg = '*already exists';
            }
        }
    }

    /**
     * delAction()
     * 
     * action to remove permissions from aclfile
     *
     * @return void
     */
    public function delAction()
    {
        $id = $this->request->get('id');
        $permissions = Permissions::find("id = '" . $id . "' ");
        $result = $permissions->delete();
        if ($result) {
            $role = Roles::find();
            $controller = Controllers::find();
            $permission = Permissions::find();
            $aclFile = APP_PATH . '/security/acl.cache';
            $acl = new Memory();

            foreach ($role as $r) {
                $acl->addRole($r->role);
            }
            foreach ($controller as $c) {
                $acl->addComponent($c->controller, ['index', 'add', 'update', 'delete']);
            }

            foreach ($permission as $p) {
                $acl->allow($p->role, $p->controller, $p->action);
            }
            file_put_contents($aclFile, serialize($acl));
        }
        $this->response->redirect('/access/buildacl?bearer=' . $this->request->get('bearer'));
    }


    /**
     * addcontrollerAction()
     * 
     * action to add controller to db after fetching from controller directory
     *
     * @return void
     */
    public function addcontrollerAction()
    {
        //caching the locale

        $this->view->locale = $this->getlocale;


        $Controller = new Controllers();
        $this->view->controllers = $Controller->getControllers();
        $dbcontroller = $Controller->getControllers();

        $checkController = 0;

        $this->view->errorMsg = "";

        $mydir = '../app/controllers';
        $mycontrollers = scandir($mydir);

        foreach ($mycontrollers as $controller) {
            $checkController = 0;
            if (strpos($controller, '.php') !== false) {
                $controller = explode("Controller.php", $controller);
                $controller = strtolower($controller[0]);
                foreach ($dbcontroller as $r) {
                    if ($r->controller == $controller) {
                        $checkController = 1;
                    }
                }
                if (!$checkController) {
                    $Controller->controller = $controller;
                    $result = $Controller->save();
                    if ($result) {
                        $this->response->redirect('/access/addcontroller?role=admin');
                    }
                }
            } else {
                continue;
            }
        }
    }

    /**
     * addactionsAction()
     * 
     * action returning response to ajax after scanning respective view directory.
     *
     * @return void
     */
    public function addactionsAction()
    {
        $controller = $this->request->getPost()['controller'];
        $mydir = "../app/views/$controller";
        $actions = scandir($mydir);
        $actionArr = array();
        foreach ($actions as $action) {
            if (strpos($action, '.phtml') !== false) {
                array_push($actionArr, $action);
            } else {
                continue;
            }
        }
        $actionArr = json_encode($actionArr);
        echo $actionArr;
        die;
    }
}
