<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use App\Models\User;
use Gettext\Translator;

class HomeController extends BaseController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        if(!empty($_SESSION['USERID'])) $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);
    }

    public function index($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/index' route");

        $this->data['login'] = User::getUserByID($_SESSION['USERID']);
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $this->data['menuActived'] = 'home';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'index.phtml', $this->data);
    }

    public function login($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/login' route");

        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'login.phtml', $this->data);
    }

    public function changePassword($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/changePassword' route");

        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'changepassword.phtml', $this->data);
    }

    public function logout($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/logout' route");

        session_destroy();

        return $response->withRedirect($this->ci->get('settings')['baseUrl'] . 'login');
    }
}
