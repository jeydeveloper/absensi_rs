<?php

namespace App\Controllers\Report;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\TransaksiModel as Transaksi;

class ReportabsenceController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/absence/list' route");

        $userId = !empty($request->getParam('userId')) ? $request->getParam('userId') : 0;
        $month = !empty($request->getParam('month')) ? $request->getParam('month') : date('m');
        $year = !empty($request->getParam('year')) ? $request->getParam('year') : date('Y');

        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['arrDayName'] = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $this->data['totalDay'] = date('t', mktime(0, 0, 0, $month, 1, $year));

        return $this->ci->get('renderer')->render($response, 'report/absence/list.phtml', $this->data);
    }
}
