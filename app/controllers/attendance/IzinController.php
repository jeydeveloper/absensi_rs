<?php

namespace App\Controllers\Attendance;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\EmployeeModel as Employee;
use App\Models\StatusModel as Status;

class IzinController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'emcu_id';
        $this->data['inputFocus'] = 'emcu_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /attendance/izin/list' route");

        $this->data['optEmployee'] = Employee::getOptNonVoid();
        $this->data['optStatusKetidakhadiran'] = Status::getOptKetidakhadiranNonVoid();

        $this->data['menuActived'] = 'attendance';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'attendance/izin/list.phtml', $this->data);
    }
}
