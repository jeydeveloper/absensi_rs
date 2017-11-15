<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\SettingModel as Setting;
use App\Helper;

class MappingjadwalApi extends \App\Api\BaseApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;

        $this->myRoleAccess = $this->getRoleAccess($_SESSION['USERID']);
    }

    public function lists($request, $response, $args)
    {
        $arrData = array(
            'data' => array()
        );

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 10;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;
        $search = !empty($request->getParam('search')) ? $request->getParam('search') : '';

        $month = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $year = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : '';

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
            $arrSchedule = Schedule::getOptNonVoid();

            $arrData['recordsTotal'] = count($resultTotal);
            $arrData['recordsFiltered'] = count($resultTotal);
            $jumlahTanggal = date('t', strtotime("$year-$month-01"));

            $dataEmpHasSchedule = [];
            $dataEmp = [];
            foreach ($result as $key => $value) {
                $dataEmp[$value->emp_code] = $value->emp_code;
            }


            if (!empty($dataEmp)) {
                $dateStart = "$year-$month-01";
                $dateEnd = date('Y-m-t', strtotime($dateStart));
                $res = Employeeschedule::getAllNonVoidWhereIn($dataEmp, $dateStart, $dateEnd);
                if (!empty($res)) {
                    foreach ($res as $key => $value) {
                        $code = !empty($value->emsc_schd_id) ? $value->schd_code : '';
                        $color = !empty($value->emsc_schd_id) ? $value->schd_color : '';
                        $dataEmpHasSchedule[$value->emsc_emp_code][$value->emsc_uniq_code] = [
                            'code' => $code,
                            'color' => $color,
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
                for ($i = $len; $i <= $forLimit; $i++) {
                    $tanggal = $cnt < 10 ? ('0' . $cnt) : $cnt;
                    $generateId = $year . $month . $tanggal . $value->emp_id;
                    $scheduleDate = $year . '-' . $month . '-' . $tanggal;
                    $lblShift = !empty($dataEmpHasSchedule[$value->emp_code][$generateId]['code']) ? $dataEmpHasSchedule[$value->emp_code][$generateId]['code'] : $this->getLabelScheduleDefault($scheduleDate);
                    $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";
                    $color = !empty($dataEmpHasSchedule[$value->emp_code][$generateId]['color']) ? $dataEmpHasSchedule[$value->emp_code][$generateId]['color'] : '';
                    $code = !empty($dataEmpHasSchedule[$value->emp_code][$generateId]['code']) ? $dataEmpHasSchedule[$value->emp_code][$generateId]['code'] : '';

                    $arrData['data'][$key][$i] = $this->getButton($generateId, $scheduleDate, $lblShift, $lblButtonStatusToUpdate, $color, $code, $value);
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

        $idChangeStatus = $request->getParam('idChangeStatus');
        $generateId = $request->getParam('generateId');
        $userId = $request->getParam('userId');
        $scheduleDate = $request->getParam('scheduleDate');
        $isDelete = !empty($request->getParam('isDelete')) ? $request->getParam('isDelete') : '';

        $lblButtonStatusToUpdate = "btnStatusToUpdate_$generateId";

        if ($isDelete) {
            $objEs = Employeeschedule::getByUniqCode($generateId);
            $objEs->delete();

            $resEmployee = Employee::find($userId);
            $empId = !empty($resEmployee->emp_id) ? $resEmployee->emp_id : '-';

            list($year, $month, $tanggal) = explode('-', $scheduleDate);
            $dayNo = date('w', mktime(0, 0, 0, $month, $tanggal, $year));
            if (!in_array($dayNo, [6, 0])) {
                $lblShift = 'NORM';
                $arrData['button'] = '<button id="' . $lblButtonStatusToUpdate . '" type="button" class="btn btn-block btn-sm" style="width:80px;background-color:#000 !important;color:#ffffff;" onclick="doAlert(\'' . $generateId . '\', \'' . $empId . '\', \'' . $lblShift . '\', \'' . $scheduleDate . '\')">' . $lblShift . '</button>';
            } else {
                $lblShift = '-';
                $arrData['button'] = '<button style="width:80px;" id="' . $lblButtonStatusToUpdate . '" type="button" class="btn btn-default btn-sm" onclick="doAlert(\'' . $generateId . '\', \'' . $empId . '\', \'' . $lblShift . '\', \'' . $scheduleDate . '\')">' . $lblShift . '</button>';
            }

            $arrData['success'] = true;
            $arrData['message'] = 'Delete data success';

            return $response->withJson($arrData);
        }

        $obj = Schedule::find($idChangeStatus);
        $emp = Employee::find($userId);
        if (!empty($obj)) {
            $objEs = Employeeschedule::getByUniqCode($generateId);
            if (!empty($objEs)) {
                $objEs->emsc_emp_id = $userId;
                $objEs->emsc_emp_code = !empty($emp->emp_code) ? $emp->emp_code : '';
                $objEs->emsc_uniq_code = $generateId;
                $objEs->emsc_schd_id = $idChangeStatus;
                $objEs->emsc_date = $scheduleDate;
                $objEs->emsc_updated_at = Helper::dateNowDB();
            } else {
                $objEs = new Employeeschedule;
                $objEs->emsc_emp_id = $userId;
                $objEs->emsc_emp_code = !empty($emp->emp_code) ? $emp->emp_code : '';
                $objEs->emsc_uniq_code = $generateId;
                $objEs->emsc_schd_id = $idChangeStatus;
                $objEs->emsc_date = $scheduleDate;
                $objEs->emsc_created_at = Helper::dateNowDB();
            }

            if ($objEs->save()) {
                $arrData['button'] = '<button id="' . $lblButtonStatusToUpdate . '" type="button" class="btn btn-block btn-sm" style="width:80px;background-color:' . $obj->schd_color . ' !important;color:#ffffff;" onclick="doAlert(' . $generateId . ', \'' . $userId . '\', \'' . $obj->schd_code . '\', \'' . $scheduleDate . '\')">' . $obj->schd_code . '</button>';

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

    private function getLabelScheduleDefault($date = '') {
        if(empty($date)) return '-';
        list($y, $m, $d) = explode('-', $date);
        $dayNo = date('w', mktime(0, 0, 0, $m, $d, $y));
        if (!in_array($dayNo, [6, 0])) {
            $text = 'NORM';
        } else {
            $text = '-';
        }
        return $text;
    }

    private function getButton($generateId = '', $scheduleDate = '', $lblShift = '', $lblButtonStatusToUpdate = '', $color = '', $code = '', $value = null) {
        $style = 'width:80px;'.(!empty($color) ? ('background-color:' . $color . ' !important;color:#ffffff;') : '');
        $onClick = !empty($code) ? ('doAlert(\'' . $generateId . '\', \'' . $value->emp_id . '\', \'' . $code . '\', \'' . $scheduleDate . '\')') : ('doAlert(\'' . $generateId . '\', \'' . $value->emp_id . '\', \'' . $lblShift . '\', \'' . $scheduleDate . '\')');

        $class = !empty($color) ? 'btn btn-block btn-sm' : 'btn btn-default btn-sm';

        $res = '<button 
        id="' . $lblButtonStatusToUpdate . '" 
        type="button" 
        class="'.$class.'" 
        style="'.$style.'" 
        onclick="'.$onClick.'">'.$lblShift.'</button>';
        return $res;
    }
}
