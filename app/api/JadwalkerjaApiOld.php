<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\TransaksiModel as Transaksi;
use App\Models\TransaksiprosesModel as Transaksiproses;
use App\Helper;

class JadwalkerjaApi
{
    protected $ci;
    protected $minuteLate;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->minuteLate = 15;
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
                  'code' => $value->schd_code,
                  'status_reason' => $value->emsc_status_reason,
                ];
              }
            }
          }

          foreach ($result as $key => $value) {
            $arrData['data'][$key] = array(
              ($key + 1),
              $value->emp_id,
              $value->emp_code,
              ('<a href="'.($this->ci->get('settings')['baseUrl'] . 'report/absence?userId='.$value->emp_id.'&month='.$month.'&year='.$year).'" class="btn-link">'.$value->emp_name.'</a>'),
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

              $tooltip = 'EMPTY';
              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $tooltip = '[SCD] ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min'] . ' - ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_max'];
              }

              $anySchedule = $tooltip;

              $absenceLabel = !empty($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min']) ? ($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min'].' - '.$dataEmpAbsence[$value->emp_code][$scheduleDate]['time_max']) : 'EMPTY';

              $tooltip .= ' | ' . $absenceLabel . ' [ABS]';

              if(!empty($dataEmpAbsence[$value->emp_code][$scheduleDate]) AND !empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $intMinSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min']));
                $intMaxSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_max']));
                $intMinAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min']);
                $intMaxAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max']);

                /*
                if(($intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) AND $intMaxAbsence >= $intMaxSchedule) {
                  $absenceLabel = 'COCOK';
                } else {
                  $absenceLabel = 'BEDA';
                }
                */
                if(($intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) OR ($intMaxAbsence >= $intMinSchedule AND $intMaxAbsence <= $intMaxSchedule) OR ($intMinAbsence <= $intMinSchedule AND $intMaxAbsence >= $intMaxSchedule)) {
                  if($intMinAbsence <= $intMinSchedule AND $intMaxAbsence >= $intMaxSchedule) {
                    $absenceLabel = 'EARLY IN | LATE OUT';
                  } elseif($intMinAbsence >= $intMinSchedule) {
                    $late = strtotime('+'.$this->minuteLate.' minutes', strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min'])));
                    if($intMinAbsence > $late) {
                      $absenceLabel = 'LATE IN';
                      if($intMaxAbsence < $intMaxSchedule) $absenceLabel .= ' | EARLY OUT';
                      if($intMaxAbsence > $intMaxSchedule) $absenceLabel .= ' | LATE OUT';
                    } elseif($intMaxAbsence < $intMaxSchedule) {
                      $absenceLabel = 'EARLY OUT';
                    } else {
                      $absenceLabel = 'MATCH';
                    }
                  } elseif($intMinAbsence < $intMinSchedule) {
                    $absenceLabel = 'EARLY IN';
                    if($intMaxAbsence < $intMaxSchedule) $absenceLabel .= ' | EARLY OUT';
                    if($intMaxAbsence > $intMaxSchedule) $absenceLabel .= ' | LATE OUT';
                  } else {
                    $absenceLabel = 'MATCH';
                  }
                } else {
                  $absenceLabel = 'ERROR';
                }
              } elseif($anySchedule != 'EMPTY' AND $absenceLabel == 'EMPTY') {
                $absenceLabel = 'ALPHA';
              } elseif($anySchedule == 'EMPTY' AND $absenceLabel != 'EMPTY') {
                $absenceLabel = 'LEMBUR';
              } else {
                $absenceLabel = 'EMPTY';
              }

              $lblShift = '-';
              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId])) $lblShift = $dataEmpHasSchedule[$value->emp_id][$generateId]['code'];

              if($absenceLabel == "EMPTY") {
                $btnStyle = 'btn-default';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
              } elseif($absenceLabel == "ALPHA") {
                $btnStyle = 'btn-warning';
                $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
              } elseif($absenceLabel == "LEMBUR") {
                $btnStyle = 'btn-success';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
              } elseif($absenceLabel == "ERROR") {
                $btnStyle = 'btn-danger';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
              } else {
                $btnStyle = 'btn-info';
                $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
              }

              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId]['status_reason'])) $tooltip .= ' | ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['status_reason'] . ' [REASON]';

              $arrData['data'][$key][$i] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-block btn-sm '.$btnStyle.'" data-toggle="tooltip" data-placement="top" title="'.$tooltip.'" '.$onClick.'>'.$absenceLabel.'</button>';
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
      $idChangeStatus = !empty($request->getParam('idChangeStatus')) ? $request->getParam('idChangeStatus') : '';
      $generateId = $request->getParam('generateId');
      $userId = $request->getParam('userId');
      $scheduleDate = $request->getParam('scheduleDate');
      $minAbsence = !empty($request->getParam('minAbsence')) ? $request->getParam('minAbsence') : '';
      $maxAbsence = !empty($request->getParam('maxAbsence')) ? $request->getParam('maxAbsence') : '';
      $intMinAbsence = !empty($minAbsence) ? strtotime($minAbsence) : 0;
      $intMaxAbsence = !empty($maxAbsence) ? strtotime($maxAbsence) : 0;
      $txtAlasan = !empty($request->getParam('txtAlasan')) ? $request->getParam('txtAlasan') : '';
      $idStatus = !empty($request->getParam('idStatus')) ? $request->getParam('idStatus') : '';

      $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";

      if(!empty($idStatus)) {
        $obj = Status::find($idStatus);
      } else {
        $obj = Schedule::find($idChangeStatus);
      }

      if(!empty($obj)) {
        $objEs = Employeeschedule::getByUniqCode($generateId);
        if(!empty($objEs)) {
          $objEs->emsc_emp_id = $userId;
          $objEs->emsc_uniq_code	 = $generateId;
          $objEs->emsc_sta_id = $idStatus;
          $objEs->emsc_date = $scheduleDate;
          $objEs->emsc_status_reason = $txtAlasan;
          $objEs->emsc_updated_at = Helper::dateNowDB();
        } else {
          $objEs = new Employeeschedule;
          $objEs->emsc_emp_id = $userId;
          $objEs->emsc_uniq_code	 = $generateId;
          $objEs->emsc_schd_id = $idChangeStatus;
          $objEs->emsc_date = $scheduleDate;
          $objEs->emsc_real_date_start = $minAbsence;
          $objEs->emsc_real_date_end = $maxAbsence;
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
        $arrData['message'] = 'Oops2.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
