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

        $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);
        $this->data['isTutupJadwal'] = $this->checkTutupJadwal();
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /attendance/izin/list' route");

        $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->data['myRoleAccess'])) ? true : false;
        $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->data['myRoleAccess'])) ? true : false;

        $arrUnitId = [];
        if($onlyUnit) {
          $res = Employee::getAllUnit($_SESSION['EMPID']);
          foreach ($res as $key => $value) {
            if(!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
          }
          if(empty($arrUnitId)) $arrUnitId[0] = 123456789;
          // print_r($arrUnitId);
        }

        $arrDivisiId = [];
        if($onlyDivisi) {
          $res = Employee::getAllDivisi($_SESSION['EMPID']);
          foreach ($res as $key => $value) {
            if(!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
          }
          if(empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
          // print_r($arrDivisiId);
        }

        $this->data['optEmployee'] = Employee::getOptNonVoid($arrUnitId, $arrDivisiId);
        $this->data['optStatusKetidakhadiran'] = Status::getOptKetidakhadiranNonVoid();

        $this->data['menuActived'] = 'attendance';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        return $this->ci->get('renderer')->render($response, 'attendance/izin/list.phtml', $this->data);
    }
}
