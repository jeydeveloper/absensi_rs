<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\UnitModel as Unit;
use App\Models\JabatanModel as Jabatan;

class EmployeeController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'emp_id';
        $this->data['inputFocus'] = 'emp_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/employee/list' route");

        $this->data['optUnit'] = Unit::getOptNonVoid();

        $this->data['optJabatan'] = Jabatan::getOptNonVoid();

        $this->data['optReligion'] = $this->ci->get('settings')['dataStatic']['religion'];
        $this->data['optStatusMarried'] = $this->ci->get('settings')['dataStatic']['statusMarried'];
        $this->data['optStatusActived'] = $this->ci->get('settings')['dataStatic']['statusActived'];

        return $this->ci->get('renderer')->render($response, 'master/employee/list.phtml', $this->data);
    }
}
