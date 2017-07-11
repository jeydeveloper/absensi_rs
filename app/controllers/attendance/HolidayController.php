<?php

namespace App\Controllers\Attendance;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\SettingModel as Setting;

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

    private function getSettingDb() {
      $arrData = [];
      $setting = Setting::getAllNonVoid();
      foreach ($setting as $key => $value) {
        $arrData[$value->sett_name] = $value->sett_value;
      }
      return $arrData;
    }

    private function getYearFilterRange() {
      $arrData = [];
      $setting = $this->getSettingDb();
      $yearBefore = !empty($setting['year_filter_range_before']) ? $setting['year_filter_range_before'] : 0;
      $yearAfter = !empty($setting['year_filter_range_after']) ? $setting['year_filter_range_after'] : 0;
      $year = date('Y');
      $startYear = $year - $yearBefore;
      $endYear = $year + $yearAfter;
      for ($i=$startYear; $i <= $endYear; $i++) {
        $arrData[$i] = $i;
      }
      return $arrData;
    }
}
