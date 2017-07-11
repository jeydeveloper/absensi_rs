<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\TransaksiModel as Transaksi;
use App\Models\TransaksiprosesModel as Transaksiproses;
use App\Models\IzinModel as Izin;
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
          $dataEmpHasCuti = [];
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
                  'color' => $value->schd_color,
                  'namaIzin' => $value->sta_name,
                  'status_reason' => $value->emsc_status_reason,
                ];
              }
            }

            $res = Izin::getAllNonVoidWhereIn($dataEmpId);
            if(!empty($res)) {
              foreach ($res as $key => $value) {
                $uniqCode = str_replace('-', '', $value->emcu_tanggal_awal) . $value->emcu_emp_id;
                $dataEmpHasCuti[$value->emcu_emp_id][$uniqCode] = [
                  'namaIzin' => $value->sta_name,
                  'keterangan' => $value->emcu_keterangan,
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

              $tooltip = 'OFF';
              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $tooltip = '[SCD] ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min'] . ' - ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_max'];
              }

              $anySchedule = $tooltip;

              $absenceLabel = !empty($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min']) ? ($dataEmpAbsence[$value->emp_code][$scheduleDate]['time_min'].' - '.$dataEmpAbsence[$value->emp_code][$scheduleDate]['time_max']) : 'OFF';

              $tooltip .= ' | ' . $absenceLabel . ' [ABS]';

              if(!empty($dataEmpAbsence[$value->emp_code][$scheduleDate]) AND !empty($dataEmpHasSchedule[$value->emp_id][$generateId])) {
                $intMinSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_min']));
                $intMaxSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['wkt_max']));
                $intMinAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min']);
                $intMaxAbsence = strtotime($dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max']);

                if(($intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) OR ($intMaxAbsence >= $intMinSchedule AND $intMaxAbsence <= $intMaxSchedule) OR ($intMinAbsence <= $intMinSchedule AND $intMaxAbsence >= $intMaxSchedule)) {
                  $absenceLabel = 'MATCH';
                } else {
                  $absenceLabel = 'ERROR';
                }
              } elseif($anySchedule != 'OFF' AND $absenceLabel == 'OFF') {
                $absenceLabel = 'ALPHA';
              } elseif($anySchedule == 'OFF' AND $absenceLabel != 'OFF') {
                $absenceLabel = 'LEMBUR';
              } else {
                $absenceLabel = 'OFF';
              }

              $lblShift = '-';
              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId])) $lblShift = $dataEmpHasSchedule[$value->emp_id][$generateId]['code'];

              if($absenceLabel == "OFF") {
                $btnCss = 'class="btn btn-block btn-sm btn-danger" style="color:#ffffff;"';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
              } elseif($absenceLabel == "ALPHA") {
                $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$value->emp_id][$generateId]['color'].' !important;color:#ffffff;"';
                $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
                $absenceLabel = '<span style="padding:4px;border-radius: 50px;;background-color:#e74c3c;color:#fff;">' . $dataEmpHasSchedule[$value->emp_id][$generateId]['code'] . '</span>';
              } elseif($absenceLabel == "LEMBUR") {
                $btnCss = 'class="btn btn-block btn-sm btn-default" style="color:#ffffff;"';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
                $absenceLabel = '<span style="padding:4px;background-color:#fff;color:#000;">L</span>';
              } elseif($absenceLabel == "ERROR") {
                $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$value->emp_id][$generateId]['color'].' !important;color:#ffffff;"';
                $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
                $absenceLabel = '<span style="padding:4px;background-color:#fff;color:#000;">' . $dataEmpHasSchedule[$value->emp_id][$generateId]['code'] . '</span>';
              } else {
                $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$value->emp_id][$generateId]['color'].' !important;color:#ffffff;"';
                $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$value->emp_id.'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$value->emp_code][$scheduleDate]['wkt_max'].'\')"';
                $absenceLabel = $dataEmpHasSchedule[$value->emp_id][$generateId]['code'];
              }

              if(!empty($dataEmpHasSchedule[$value->emp_id][$generateId]['status_reason'])) {
                $tooltip .= ' | ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['namaIzin'] . ' - ' . $dataEmpHasSchedule[$value->emp_id][$generateId]['status_reason'] . ' [REASON]';
              } elseif(!empty($dataEmpHasCuti[$value->emp_id][$generateId])) {
                $tooltip .= ' | ' . $dataEmpHasCuti[$value->emp_id][$generateId]['namaIzin'] . ' - ' . $dataEmpHasCuti[$value->emp_id][$generateId]['keterangan'] . ' [REASON]';
              }

              $arrData['data'][$key][$i] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" '.$btnCss.'" data-toggle="tooltip" data-placement="top" title="'.$tooltip.'" '.$onClick.'>'.$absenceLabel.'</button>';
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
          $resEmployee = Employee::find($userId);
          $empCode = !empty($resEmployee->emp_code) ? $resEmployee->emp_code : '-';

          $arrParam = [
            'dateStart' => $scheduleDate,
            'dateEnd' => $scheduleDate,
            'dataEmp' => array($empCode),
            'dataEmpId' => array($userId),
            'empId' => $userId,
            'empCode' => $empCode,
            'generateId' => $generateId,
            'scheduleDate' => $scheduleDate,
          ];

          $arrData['button'] = $this->getButtonJadwal($arrParam);

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

    private function getButtonJadwal($arrParam = null) {
      if(!is_array($arrParam)) return 0;

      $dataEmpAbsence = [];
      $dataEmpHasSchedule = [];
      $dataEmpHasCuti = [];

      $res = Transaksi::getAllMinMaxTranTime($arrParam['dateStart'], $arrParam['dateEnd'], $arrParam['dataEmp']);
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

      $res = Employeeschedule::getAllNonVoidWhereIn($arrParam['dataEmpId']);
      if(!empty($res)) {
        foreach ($res as $key => $value) {
          $dataEmpHasSchedule[$value->emsc_emp_id][$value->emsc_uniq_code] = [
            'wkt_min' => $value->schd_waktu_awal,
            'wkt_max' => $value->schd_waktu_akhir,
            'code' => $value->schd_code,
            'color' => $value->schd_color,
            'namaIzin' => $value->sta_name,
            'status_reason' => $value->emsc_status_reason,
          ];
        }
      }

      $res = Izin::getAllNonVoidWhereIn($arrParam['dataEmpId']);
      if(!empty($res)) {
        foreach ($res as $key => $value) {
          $uniqCode = str_replace('-', '', $value->emcu_tanggal_awal) . $value->emcu_emp_id;
          $dataEmpHasCuti[$value->emcu_emp_id][$uniqCode] = [
            'namaIzin' => $value->sta_name,
            'keterangan' => $value->emcu_keterangan,
          ];
        }
      }

      $generateId = $arrParam['generateId'];
      $scheduleDate = $arrParam['scheduleDate'];
      $lblShift = '-';
      $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";

      $tooltip = 'OFF';
      if(!empty($dataEmpHasSchedule[$arrParam['empId']][$generateId])) {
        $tooltip = '[SCD] ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['wkt_min'] . ' - ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['wkt_max'];
      }

      $anySchedule = $tooltip;

      $absenceLabel = !empty($dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['time_min']) ? ($dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['time_min'].' - '.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['time_max']) : 'OFF';

      $tooltip .= ' | ' . $absenceLabel . ' [ABS]';

      if(!empty($dataEmpAbsence[$arrParam['empCode']][$scheduleDate]) AND !empty($dataEmpHasSchedule[$arrParam['empId']][$generateId])) {
        $intMinSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['wkt_min']));
        $intMaxSchedule = strtotime(($scheduleDate . ' ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['wkt_max']));
        $intMinAbsence = strtotime($dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_min']);
        $intMaxAbsence = strtotime($dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_max']);

        if(($intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) OR ($intMaxAbsence >= $intMinSchedule AND $intMaxAbsence <= $intMaxSchedule) OR ($intMinAbsence <= $intMinSchedule AND $intMaxAbsence >= $intMaxSchedule)) {
          $absenceLabel = 'MATCH';
        } else {
          $absenceLabel = 'ERROR';
        }
      } elseif($anySchedule != 'OFF' AND $absenceLabel == 'OFF') {
        $absenceLabel = 'ALPHA';
      } elseif($anySchedule == 'OFF' AND $absenceLabel != 'OFF') {
        $absenceLabel = 'LEMBUR';
      } else {
        $absenceLabel = 'OFF';
      }

      $lblShift = '-';
      if(!empty($dataEmpHasSchedule[$arrParam['empId']][$generateId])) $lblShift = $dataEmpHasSchedule[$arrParam['empId']][$generateId]['code'];

      if($absenceLabel == "OFF") {
        $btnCss = 'class="btn btn-block btn-sm btn-danger" style="color:#ffffff;"';
        $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$arrParam['empId'].'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
      } elseif($absenceLabel == "ALPHA") {
        $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$arrParam['empId']][$generateId]['color'].' !important;color:#ffffff;"';
        $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$arrParam['empId'].'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \'\', \'\')"';
        $absenceLabel = '<span style="padding:4px;border-radius: 50px;;background-color:#e74c3c;color:#fff;">' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['code'] . '</span>';
      } elseif($absenceLabel == "LEMBUR") {
        $btnCss = 'class="btn btn-block btn-sm btn-default" style="color:#ffffff;"';
        $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$arrParam['empId'].'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_max'].'\')"';
        $absenceLabel = '<span style="padding:4px;background-color:#fff;color:#000;">L</span>';
      } elseif($absenceLabel == "ERROR") {
        $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$arrParam['empId']][$generateId]['color'].' !important;color:#ffffff;"';
        $onClick = 'onclick="doAlert(\''.$generateId.'\', \''.$arrParam['empId'].'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_max'].'\')"';
        $absenceLabel = '<span style="padding:4px;background-color:#fff;color:#000;">' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['code'] . '</span>';
      } else {
        $btnCss = 'class="btn btn-block btn-sm" style="background-color:'.$dataEmpHasSchedule[$arrParam['empId']][$generateId]['color'].' !important;color:#ffffff;"';
        $onClick = 'onclick="doAlertPopup(\''.$generateId.'\', \''.$arrParam['empId'].'\', \''.$lblShift.'\', \''.$scheduleDate.'\', \''.$absenceLabel.'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_min'].'\', \''.$dataEmpAbsence[$arrParam['empCode']][$scheduleDate]['wkt_max'].'\')"';
        $absenceLabel = $dataEmpHasSchedule[$arrParam['empId']][$generateId]['code'];
      }

      if(!empty($dataEmpHasSchedule[$arrParam['empId']][$generateId]['status_reason'])) {
        $tooltip .= ' | ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['namaIzin'] . ' - ' . $dataEmpHasSchedule[$arrParam['empId']][$generateId]['status_reason'] . ' [REASON]';
      } elseif(!empty($dataEmpHasCuti[$arrParam['empId']][$generateId])) {
        $tooltip .= ' | ' . $dataEmpHasCuti[$arrParam['empId']][$generateId]['namaIzin'] . ' - ' . $dataEmpHasCuti[$arrParam['empId']][$generateId]['keterangan'] . ' [REASON]';
      }

      return '<button id="'.$lblButtonStatusToUpdate.'" type="button" '.$btnCss.'" data-toggle="tooltip" data-placement="top" title="'.$tooltip.'" '.$onClick.'>'.$absenceLabel.'</button>';
    }
}
