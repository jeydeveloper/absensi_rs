<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\TransaksiModel as Transaksi;
use App\Models\TransaksiprosesModel as Transaksiproses;
use App\Helper;

class JadwalkerjaApi
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

        // echo $request->getParam('start'); exit();

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 10;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;

        $month = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $year = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');

        $resultTotal = Employee::getAllNonVoid();
        $result = Employee::getAllNonVoid($limit, $offset);
        if(!empty($result)) {
          $arrData['recordsTotal'] = count($resultTotal);
          $arrData['recordsFiltered'] = count($resultTotal);
          $jumlahTanggal = date('t', strtotime("$year-$month-01"));

          $dataEmpAbsence = [];
          $dataEmpHasSchedule = [];
          $dataEmp = [];
          $dataEmpId = [];
          foreach ($result as $key => $value) {
            $dataEmp[$value->emp_id] = $value->emp_code;
            $dataEmpId[$value->emp_id] = $value->emp_id;
          }

          if(!empty($dataEmp)) {
            $dateStart = $year.'-'.$month.'-01';
            $dateEnd = date('Y-m-t', strtotime($year.'-'.$month.'-01'));
            $res = Transaksi::getAllMinMaxTranTime($dateStart, $dateEnd, $dataEmp);
            if(!empty($res)) {
              foreach ($res as $key => $value) {
                $dataEmpAbsence[$value->tran_cardNo][$value->tgl] = [
                  'wkt_min' => $value->wkt_min,
                  'wkt_max' => $value->wkt_max,
                  'time_min' => $value->time_min,
                  'time_max' => $value->time_max,
                ];
              }
            }
          }

          if(!empty($dataEmpId)) {
            $res = Employeeschedule::getAllNonVoidWhereIn($dataEmpId);
            if(!empty($res)) {
              foreach ($res as $key => $value) {
                $dataEmpHasSchedule[$value->emsc_emp_id][$value->emsc_uniq_code] = [
                  'wkt_min' => $value->schd_waktu_awal,
                  'wkt_max' => $value->schd_waktu_akhir,
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

              $absenceLabel = !empty($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min']) ? ($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min'].' - '.$dataEmpAbsence[$value->emp_code][$scheduleDate]['time_max']) : 'EMPTY';
              if(!empty($dataEmpAbsence[$value->emp_code][$scheduleDate]) AND !empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $intMinSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min']));
                $intMaxSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_max']));
                $intMinAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min']);
                $intMaxAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max']);

                if(($intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) AND $intMaxAbsence >= $intMaxSchedule) {
                  $absenceLabel = 'COCOK';
                } else {
                  $absenceLabel = 'BEDA';
                }
              }

              $arrData['data'][$key][$i] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-block btn-sm btn-danger">'.$absenceLabel.'</button>';
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
}
