<?php

namespace App\Controllers\Proses;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;

class JadwalkerjaController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'sta_id';
        $this->data['inputFocus'] = 'sta_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /proses/jadwalkerja/list' route");

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'].'-'.$this->data['selectedMonth'].'-01'));
        $this->data['listMonth'] = $this->ci->get('settings')['dataStatic']['listMonth'];
        $this->data['listYear'] = $this->ci->get('settings')['dataStatic']['listYear'];

        $this->data['listSchedule'] = Schedule::getAllNonVoid();
        $this->data['listStatusKehadiran'] = Status::getAllKehadiranNonVoid();
        $this->data['listStatusKetidakhadiran'] = Status::getAllKetidakhadiranNonVoid();

        $this->data['menuActived'] = 'prosesAbsensi';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'proses/jadwalkerja/list.phtml', $this->data);
    }
}
