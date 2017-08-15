<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\RoleaccessModel as Roleaccess;

class UseradminController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'usr_id';
        $this->data['inputFocus'] = 'usr_username';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/useradmin/list' route");

        $this->data['menuActived'] = 'master';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['optRoleAccess'] = Roleaccess::getOptNonVoid();

        return $this->ci->get('renderer')->render($response, 'master/useradmin/list.phtml', $this->data);
    }
}
