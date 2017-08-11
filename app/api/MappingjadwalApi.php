<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\MappingjadwalModel as Mappingjadwal;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\SettingModel as Setting;
use App\Helper;

class MappingjadwalApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $arrData = array(
          'data' => array()
        );

        $setting = $this->getSettingDb();

        // echo $request->getParam('start'); exit();

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 10;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;

        $month = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $year = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');

        // $result = Mappingjadwal::getAllNonVoid();
        $resultTotal = Employee::getAllNonVoid();
        $result = Employee::getAllNonVoid($limit, $offset);
        if(!empty($result)) {
          $arrData['recordsTotal'] = count($resultTotal);
          $arrData['recordsFiltered'] = count($resultTotal);
          $jumlahTanggal = date('t', strtotime("$year-$month-01"));

          $dataEmpHasSchedule = [];
          $dataEmp = [];
          foreach ($result as $key => $value) {
            $dataEmp[$value->emp_id] = $value->emp_id;
          }

          if(!empty($dataEmp)) {
            $res = Employeeschedule::getAllNonVoidWhereIn($dataEmp);
            if(!empty($res)) {
              foreach ($res as $key => $value) {
                $dataEmpHasSchedule[$value->emsc_emp_id][$value->emsc_uniq_code] = [
                  'code' => $value->schd_code,
                  'color' => $value->schd_color,
                ];
              }
            }
          }

          foreach ($result as $key => $value) {
            $arrData['data'][$key] = array(
              ($key + 1),
              $value->emp_id,
              $value->emp_code,
              $value->emp_name,
            );
            $len = count($arrData['data'][$key]);
            $forLimit = $jumlahTanggal + $len;

            $cnt = 1;
            for ($i=$len; $i <= $forLimit; $i++) {
              $tanggal = $cnt < 10 ? ('0'.$cnt) : $cnt;
              $generateId = $year . $month . $tanggal . $value->emp_id;
              $scheduleDate = $year . '-' . $month . '-' . $tanggal;
              $lblShift = '-';
              $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";

              if(empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $wktMin = $setting['default_1_schedule_in'];
                $wktMax = $setting['default_1_schedule_out'];
                $dataEmpHasSchedule[$value->emp_id][$generateId] = [
                  'wkt_min' => $wktMin,
                  'wkt_max' => $wktMax,
                  'code' => 'NORM',
                  'color' => '#000000',
                  'namaIzin' => '',
                  'status_reason' => '',
                  'isScheduleGantiHari' => 0,
                ];
              }

              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $arrData['data'][$key][$i] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-block btn-sm" style="width:60px;background-color:'.$dataEmpHasSchedule[$value->emp_id][$generateId]['color'].' !important;color:#ffffff;" onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$dataEmpHasSchedule[$value->emp_id][$generateId]['code'].'\', \''.$scheduleDate.'\')">'.$dataEmpHasSchedule[$value->emp_id][$generateId]['code'].'</button>';
              } else {
                $arrData['data'][$key][$i] = '<button style="width:60px;" id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-default btn-sm" onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\')">'.$lblShift.'</button>';
              }
              $cnt++;
            }
          }
        }

        return $response->withJson($arrData);
    }

    public function doEdit($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $hdStatus = $request->getParam('hdStatus');
      $idChangeStatus = $request->getParam('idChangeStatus');
      $txtAlasan = $request->getParam('txtAlasan');
      $generateId = $request->getParam('generateId');
      $userId = $request->getParam('userId');
      $scheduleDate = $request->getParam('scheduleDate');

      $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";

      $obj = Schedule::find($idChangeStatus);
      if(!empty($obj)) {
        $objEs = Employeeschedule::getByUniqCode($generateId);
        if(!empty($objEs)) {
          $objEs->emsc_emp_id = $userId;
          $objEs->emsc_uniq_code	 = $generateId;
          $objEs->emsc_schd_id = $idChangeStatus;
          $objEs->emsc_date = $scheduleDate;
          $objEs->emsc_updated_at = Helper::dateNowDB();
        } else {
          $objEs = new Employeeschedule;
          $objEs->emsc_emp_id = $userId;
          $objEs->emsc_uniq_code	 = $generateId;
          $objEs->emsc_schd_id = $idChangeStatus;
          $objEs->emsc_date = $scheduleDate;
          $objEs->emsc_created_at = Helper::dateNowDB();
        }

        if($objEs->save()) {
          $arrData['button'] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-block btn-sm" style="background-color:'.$obj->schd_color.' !important;color:#ffffff;" onclick="doAlert('.$generateId.', \''.$userId.'\', \''.$obj->schd_code.'\', \''.$scheduleDate.'\')">'.$obj->schd_code.'</button>';

          $arrData['success'] = true;
          $arrData['message'] = 'Update data success';
        } else {
          $arrData['message'] = 'Oops.. please try again!';
        }
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }

    public function getSettingDb() {
      $arrData = [];
      $setting = Setting::getAllNonVoid();
      foreach ($setting as $key => $value) {
        $arrData[$value->sett_name] = $value->sett_value;
      }
      return $arrData;
    }
}
