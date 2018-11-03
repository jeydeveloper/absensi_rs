<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\EmployeeModel as Employee;
use App\Models\StatusModel as Status;
use App\Models\SettingModel as Setting;
use App\Models\ScheduleModel as Schedule;
use App\Helper;

class ReportApi
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

        $result = Employee::getAllNonVoid();
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $arrData['data'][] = array(
                    ($key + 1),
                    $value->emp_id,
                    $value->emp_code,
                    $value->emp_name
                );
            }
        }

        return $response->withJson($arrData);
    }

    public function summary($request, $response, $args)
    {
        $arrData = array(
            'data' => array()
        );

        $setting = $this->getSettingDb();

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 10;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;
        $search = !empty($request->getParam('search')) ? $request->getParam('search') : '';

        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : '';
        // $empId = 560;

        $startDate = !empty($request->getParam('startdate')) ? $request->getParam('startdate') : '';
        $endDate = !empty($request->getParam('enddate')) ? $request->getParam('enddate') : '';
        $startDate = $this->formatDateDb($startDate);
        $endDate = $this->formatDateDb($endDate);

        $bagianId = !empty($request->getParam('bagianId')) ? $request->getParam('bagianId') : '';
        $unitId = !empty($request->getParam('unitId')) ? $request->getParam('unitId') : '';

        if(!empty($bagianId)) {
            $arrDivisiId[0] = $bagianId;
        } elseif(!empty($unitId)) {
            $arrUnitId[0] = $unitId;
        } else {
            $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->myRoleAccess)) ? true : false;
            $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->myRoleAccess)) ? true : false;

            $arrUnitId = [];
            if ($onlyUnit) {
                $res = Employee::getAllUnit($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
                }
                if (empty($arrUnitId)) $arrUnitId[0] = 123456789;
            }

            $arrDivisiId = [];
            if ($onlyDivisi) {
                $res = Employee::getAllDivisi($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
                }
                if (empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
            }
        }


        $resultTotal = Employee::getAllNonVoid('', '', $search, $arrUnitId, $arrDivisiId, $empId);
        $result = Employee::getAllNonVoid($limit, $offset, $search, $arrUnitId, $arrDivisiId, $empId);
        if (!empty($result)) {

            $arrData['recordsTotal'] = count($resultTotal);
            $arrData['recordsFiltered'] = count($resultTotal);

            $arrJumlah = [];
            $dataEmpHasStatus = [];
            $dataEmp = [];
            foreach ($result as $key => $value) {
                $dataEmp[$value->emp_code] = $value->emp_code;
            }


            if (!empty($dataEmp)) {
                $res = Employeeschedule::getAllNonVoidWhereIn($dataEmp, $startDate, $endDate, true);
                // print_r($res);
                if (!empty($res)) {
                    foreach ($res as $key => $value) {
                        if(empty($dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id])) {
                            $dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id] = $value->sta_sanksi;
                        } else {
                            $dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id] += $value->sta_sanksi;
                        }

                        if(empty($value->sta_id)) {
                            $dayNo = date('w', strtotime($value->tgl));
                            // echo "string".$dayNo;
                            if (!in_array($dayNo, [6, 0])) {
                                if(empty($value->schd_waktu_awal) AND empty($value->schd_waktu_akhir)) {
                                    if ($dayNo == 5) {
                                        $minSchedule = $value->tgl . ' ' . $setting['default_2_schedule_in'];
                                        $maxSchedule = $value->tgl . ' ' . $setting['default_2_schedule_out'];
                                    } else {
                                        $minSchedule = $value->tgl . ' ' . $setting['default_1_schedule_in'];
                                        $maxSchedule = $value->tgl . ' ' . $setting['default_1_schedule_out'];
                                    }
                                } else {
                                    $minSchedule = $value->tgl . ' ' . $value->schd_waktu_awal;
                                    $maxSchedule = $value->tgl . ' ' . $value->schd_waktu_akhir;
                                }
                            } else {
                                $minSchedule = $value->tgl . ' ' . $value->schd_waktu_awal;
                                $maxSchedule = $value->tgl . ' ' . $value->schd_waktu_akhir;
                            }
                            
                            $intMinSchedule = strtotime($minSchedule);
                            $intMaxSchedule = strtotime($maxSchedule);
                            $intMinAbsence = strtotime($value->wkt_min);
                            $intMaxAbsence = strtotime($value->wkt_max);

                            $telat = false;
                            $pulangCepat = false;
                            $totalMinuteLate = 0;
                            $totalMinuteEearlyOut = 0;

                            if (((!empty($intMinAbsence) AND $intMinAbsence >= $intMinSchedule AND $intMinAbsence <= $intMaxSchedule) OR (!empty($intMaxAbsence) AND $intMaxAbsence >= $intMinSchedule AND $intMaxAbsence <= $intMaxSchedule) OR ($intMinAbsence <= $intMinSchedule AND $intMaxAbsence >= $intMaxSchedule))) {
                                $earlyOut = strtotime('-' . $setting['toleransi_out'] . ' minutes', $intMaxSchedule);
                                $late = strtotime('+' . $setting['toleransi_in'] . ' minutes', $intMinSchedule);
                                if ($intMinAbsence > $late) {
                                    $statusLate = true;
                                    $totalMinuteLate = date('h:i', ($intMinAbsence - $intMinSchedule));
                                    list($hour, $minute) = explode(':', $totalMinuteLate);
                                    $hour = (int)$hour;
                                    $minute = (int)$minute;
                                    $hour = $hour > 0 ? (($hour - 1) * 60) : 0;
                                    $minute += $hour;
                                    $totalMinuteLate = $minute;
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] = 0;
                                    // $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] += $minute;
                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] += 1;
                                    $telat = true;
                                }
                                if ($intMaxAbsence < $earlyOut) {
                                    $statusEearlyOut = true;
                                    $totalMinuteEearlyOut = date('h:i', ($intMaxSchedule - $intMaxAbsence));
                                    list($hour, $minute) = explode(':', $totalMinuteEearlyOut);
                                    $hour = (int)$hour;
                                    $minute = (int)$minute;
                                    $hour = $hour > 0 ? (($hour - 1) * 60) : 0;
                                    $minute += $hour;
                                    $totalMinuteEearlyOut = $minute;
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] = 0;
                                    // $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] += $minute;
                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] += 1;
                                    $pulangCepat = true;
                                }
                            }

                            if (!empty($value->time_min) AND !empty($value->time_max) AND $value->time_min == $value->time_max) {
                                $settingBatasAbsenMasuk = $setting['batas_absen_masuk'] * 60;
                                $batasAbsenMasuk = strtotime('+' . $settingBatasAbsenMasuk . ' minutes', $intMinSchedule);

                                // if (!empty($totalMinuteLate)) $arrJumlah['jumlahMenitTerlambat'] -= $totalMinuteLate;
                                if (!empty($totalMinuteLate)) $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] -= 1;
                                // if (!empty($totalMinuteEearlyOut)) $arrJumlah['jumlahMenitPulangCepat'] -= $totalMinuteEearlyOut;
                                if (!empty($totalMinuteEearlyOut)) $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] -= 1;

                                if ($intMinAbsence <= $batasAbsenMasuk) {
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] = 0;
                                    // $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] += $totalMinuteEearlyOut;
                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] += 1;
                                    $pulangCepat = true;
                                } else {
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] = 0;
                                    // $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] += $totalMinuteLate;
                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] += 1;
                                    $telat = true;
                                }
                            }

                            if (in_array($dayNo, [6, 0]) AND (!empty($value->time_min) OR !empty($value->time_max))) {
                                if ($pulangCepat AND !empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] -= 1;
                                if ($telat AND !empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] -= 1;
                            }
                        } else {
                            //untuk izin
                            if(empty($arrJumlah[$value->emsc_emp_code][$value->sta_id])) $arrJumlah[$value->emsc_emp_code][$value->sta_id] = 0;
                            $arrJumlah[$value->emsc_emp_code][$value->sta_id] += 1;
                        }

                        if(!empty($value->emsc_schd_id)) {
                            //untuk schedule
                            if(empty($arrJumlah[$value->emsc_emp_code]['shift'][$value->emsc_schd_id])) $arrJumlah[$value->emsc_emp_code]['shift'][$value->emsc_schd_id] = 0;
                            $arrJumlah[$value->emsc_emp_code]['shift'][$value->emsc_schd_id] += 1;
                        }
                    }
                }
            }

            $res = Status::getAllKetidakhadiranNonVoid('sta_name');
            $res2 = Schedule::getForReportNonVoid();
            foreach ($result as $key => $value) {
                $arrData['data'][$key] = array(
                    ($key + 1),
                    $value->emp_id,
                    $value->emp_code,
                    $value->emp_name,
                    (!empty($arrJumlah[$value->emp_code]['jumlahMenitTerlambat']) ? $arrJumlah[$value->emp_code]['jumlahMenitTerlambat'] : ''),
                    (!empty($arrJumlah[$value->emp_code]['jumlahMenitPulangCepat']) ? $arrJumlah[$value->emp_code]['jumlahMenitPulangCepat'] : ''),
                );

                $cnt = 6;
                if(!empty($res)) {
                    foreach ($res as $key2 => $value2) {
                        // $arrData['data'][$key][($cnt+$key2)] = !empty($dataEmpHasStatus[$value->emp_code][$value2->sta_id]) ? $dataEmpHasStatus[$value->emp_code][$value2->sta_id] : '';
                        $arrData['data'][$key][($cnt+$key2)] = !empty($arrJumlah[$value->emp_code][$value2->sta_id]) ? $arrJumlah[$value->emp_code][$value2->sta_id] : '';
                    }
                }

                $cnt = $cnt + ((!empty($res)) ? count($res) : 0);
                if(!empty($res2)) {
                    foreach ($res2 as $key2 => $value2) {
                        $arrData['data'][$key][($cnt+$key2)] = !empty($arrJumlah[$value->emp_code]['shift'][$value2['schd_id']]) ? $arrJumlah[$value->emp_code]['shift'][$value2['schd_id']] : '';
                    }
                }
            }
        }

        return $response->withJson($arrData);
    }

    private function formatDateDb($param = '') {
        $param = explode('/', $param);
        return ($param[2] . '-' . $param[0] . '-' . $param[1]);
    }

    public function getSettingDb()
    {
        $arrData = [];
        $setting = Setting::getAllNonVoid();
        foreach ($setting as $key => $value) {
            $arrData[$value->sett_name] = $value->sett_value;
        }
        return $arrData;
    }
}
