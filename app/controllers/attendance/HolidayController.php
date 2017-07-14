<?php

namespace App\Controllers\Attendance;

use Interop\Container\ContainerInterface;
use Gettext\Translator;

class HolidayController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'hol_id';
        $this->data['inputFocus'] = 'hol_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /attendance/holiday/list' route");

        $this->data['menuActived'] = 'attendance';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['yearFilterRange'] = $this->getYearFilterRange();

        return $this->ci->get('renderer')->render($response, 'attendance/holiday/list.phtml', $this->data);
    }

}
