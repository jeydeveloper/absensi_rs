<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\BagianModel as Bagian;

class UnitController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'uni_id';
        $this->data['inputFocus'] = 'uni_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);
        $this->data['isTutupJadwal'] = $this->checkTutupJadwal();
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/unit/list' route");

        $this->data['optBagian'] = Bagian::getOptNonVoid();

        $this->data['menuActived'] = 'master';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'master/unit/list.phtml', $this->data);
    }
}
