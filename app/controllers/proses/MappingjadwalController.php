<?php

namespace App\Controllers\Proses;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\ScheduleModel as Schedule;

class MappingjadwalController extends \App\Controllers\BaseController
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
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /proses/mappingjadwal/list' route");

        $this->data['listSchedule'] = Schedule::getAllNonVoid();

        $this->data['menuActived'] = 'prosesAbsensi';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'proses/mappingjadwal/list.phtml', $this->data);
    }
}