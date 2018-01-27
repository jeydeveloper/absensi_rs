<?php

namespace App\Controllers\Proses;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;
use App\Models\BagianModel as Bagian;
use App\Models\UnitModel as Unit;

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

        $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);
        $this->data['isTutupJadwal'] = $this->checkTutupJadwal();
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /proses/jadwalkerja/list' route");

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'].'-'.$this->data['selectedMonth'].'-01'));
        $this->data['listMonth'] = $this->getMonthFilter();

        $this->data['listSchedule'] = Schedule::getAllNonVoid();
        $this->data['listStatusKehadiran'] = Status::getAllKehadiranNonVoid();
        $this->data['listStatusKetidakhadiran'] = Status::getAllKetidakhadiranNonVoid();

        $this->data['menuActived'] = 'prosesAbsensi';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['yearFilterRange'] = $this->getYearFilterRange();

        return $this->ci->get('renderer')->render($response, 'proses/jadwalkerja/list.phtml', $this->data);
    }

    public function detail($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /proses/jadwalkerja/detail' route");

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'].'-'.$this->data['selectedMonth'].'-01'));
        $this->data['listMonth'] = $this->getMonthFilter();

        $this->data['listSchedule'] = Schedule::getAllNonVoid();
        $this->data['listStatusKehadiran'] = Status::getAllKehadiranNonVoid();
        $this->data['listStatusKetidakhadiran'] = Status::getAllKetidakhadiranNonVoid();

        $this->data['menuActived'] = 'report';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['yearFilterRange'] = $this->getYearFilterRange();

        $this->data['optBagian'] = Bagian::getOptNonVoid();
        $this->data['optUnit'] = Unit::getOptNonVoid();

        return $this->ci->get('renderer')->render($response, 'proses/jadwalkerja/detail.phtml', $this->data);
    }
}
