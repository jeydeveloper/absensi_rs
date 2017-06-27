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
    }

    public function index($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/index' route");

        $data['login'] = User::getUserByID($_SESSION['USERID']);
        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $data['menuActived'] = 'home';
        $data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $data);

        return $this->ci->get('renderer')->render($response, 'index.phtml', $data);
    }

    public function login($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/login' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'login.phtml', $data);
    }

    public function logout($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton '/home/logout' route");

        session_destroy();

        return $response->withRedirect($this->ci->get('settings')['baseUrl'] . 'login');
    }
}
