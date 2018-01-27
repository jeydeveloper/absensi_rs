<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\EmployeeModel as Employee;
use App\Models\StatusModel as Status;
use App\Models\SettingModel as Setting;
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

        $startDate = !empty($request->getParam('startdate')) ? $request->getParam('startdate') : '';
        $endDate = !empty($request->getParam('enddate')) ? $request->getParam('enddate') : '';
        $startDate = $this->formatDateDb($startDate);
        $endDate = $this->formatDateDb($endDate);

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
                $res = Employeeschedule::getAllNonVoidWhereIn($dataEmp, $startDate, $endDate);
                if (!empty($res)) {
                    foreach ($res as $key => $value) {
                        if(empty($dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id])) {
                            $dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id] = $value->sta_sanksi;
                        } else {
                            $dataEmpHasStatus[$value->emsc_emp_code][$value->sta_id] += $value->sta_sanksi;
                        }

                        /*if(empty($value->sta_id)) {
                            if (!empty($value->time_min) AND !empty($value->time_max) AND $value->time_min == $value->time_max) {
                                $intMinAbsence = !empty($value->wkt_min) ? strtotime($value->wkt_min) : '';

                                $settingBatasAbsenMasuk = $setting['batas_absen_masuk'] * 60;
                                $batasAbsenMasuk = strtotime('+' . $settingBatasAbsenMasuk . ' minutes', strtotime(($value->emsc_date . ' ' . $value->wkt_min)));

                                if ($intMinAbsence <= $batasAbsenMasuk) {
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] = 0;

                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitPulangCepat'] += $setting['sanksi_tidak_absen'];
                                } else {
                                    if(empty($arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'])) $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] = 0;

                                    $arrJumlah[$value->emsc_emp_code]['jumlahMenitTerlambat'] += $setting['sanksi_tidak_absen'];
                                }
                            }
                        }*/
                    }
                }
            }

            $res = Status::getAllKetidakhadiranNonVoid('sta_name');
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
                        $arrData['data'][$key][($cnt+$key2)] = !empty($dataEmpHasStatus[$value->emp_code][$value2->sta_id]) ? $dataEmpHasStatus[$value->emp_code][$value2->sta_id] : '';
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
