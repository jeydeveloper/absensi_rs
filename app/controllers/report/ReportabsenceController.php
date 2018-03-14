<?php

namespace App\Controllers\Report;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\EmployeeModel as Employee;
use App\Models\ScheduleModel as Schedule;
use App\Models\StatusModel as Status;
use App\Models\EmployeescheduleModel as Employeeschedule;
use App\Models\TransaksiModel as Transaksi;
use App\Models\IzinModel as Izin;
use App\Models\SettingModel as Setting;
use App\Models\BagianModel as Bagian;

class ReportabsenceController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);

        $this->data['actualLink'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/absence/list' route");

        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : '';
        if(!empty($_SESSION['GUEST'])) $empId = $_SESSION['EMPID'];

        $month = !empty($request->getParam('month')) ? $request->getParam('month') : '';
        $year = !empty($request->getParam('year')) ? $request->getParam('year') : date('Y');

        if (empty($month)) {
            $response = $response->withRedirect($this->data['baseUrl'] . 'report/tahunan?year=' . $year . '&empId=' . $empId);
            return $response;
        }

        $this->data['settings'] = $tmp = $this->getSettingDb();
        $tanggalCutoffSpecial = $this->getTanggalCutoffSpecial($this->data['settings']['tanggal_cutoff_special']);
        if(!empty($tanggalCutoffSpecial['bulan']) AND $tanggalCutoffSpecial['bulan'] == (int)$month) {
            $this->data['settings']['tanggal_cutoff'] = !empty($tanggalCutoffSpecial['tanggalAwal']) ? $tanggalCutoffSpecial['tanggalAwal'] : 1;
        }

        //print_r($this->data['settings']); exit();

        if(!empty($tmp['tanggal_cutoff']) AND $tmp['tanggal_cutoff'] > 1) {
            $arrLastMont = $this->getLastMonth($month, $year);
            $month = $arrLastMont['month'];
            $year = $arrLastMont['year'];
        }

        $bagianId = !empty($request->getParam('bagianId')) ? $request->getParam('bagianId') : '';
        $unitId = !empty($request->getParam('unitId')) ? $request->getParam('unitId') : '';

        $arrEmpId = [];

        if (!empty($empId)) {
            $emp = new \stdClass();
            $emp->emp_id = $empId;
            $arrEmpId[0] = $emp;
        } else {
            if (!empty($bagianId)) {
                $arrEmpId = Employee::getEmployeeByBagian($bagianId);
            }
            if (!empty($unitId)) {
                $arrEmpId = Employee::getEmployeeByUnit($unitId);
            }

            $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->data['myRoleAccess'])) ? true : false;
            $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->data['myRoleAccess'])) ? true : false;

            $arrUnitId = [];
            $arrDivisiId = [];
            if ($onlyUnit) {
                $res = Employee::getAllUnit($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
                }
                if (empty($arrUnitId)) $arrUnitId[0] = 123456789;
                $arrEmpId = Employee::getEmployeeByUnit($arrUnitId);
            } elseif ($onlyDivisi) {
                $res = Employee::getAllDivisi($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
                }
                if (empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
                $arrEmpId = Employee::getEmployeeByBagian($arrDivisiId);
            }
        }

        $this->data['data'] = [];

        if (!empty($arrEmpId)) {
            foreach ($arrEmpId as $key => $value) {
                $this->data['data'][$key]['month'] = $month;
                $this->data['data'][$key]['year'] = $year;
                $this->data['data'][$key]['arrDayName'] = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $this->data['data'][$key]['arrMonthName'] = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $this->data['data'][$key]['totalDay'] = date('t', mktime(0, 0, 0, $month, 1, $year));

                $this->data['data'][$key]['izinTidakHadir'] = Status::getAllKetidakhadiranNonVoid();
                $this->data['data'][$key]['cntIzinTidakHadir'] = count($this->data['data'][$key]['izinTidakHadir']);
                $this->data['data'][$key]['employee'] = Employee::getEmployeeByID($value->emp_id);

                $empCode = !empty($this->data['data'][$key]['employee']->emp_code) ? $this->data['data'][$key]['employee']->emp_code : '-';

                $this->data['data'][$key]['empId'] = $value->emp_id;
                $this->data['data'][$key]['empCode'] = $empCode;

                $this->data['data'][$key]['setting'] = $this->getSettingDb();

                $tanggalCutoffSpecial = $this->getTanggalCutoffSpecial($this->data['data'][$key]['setting']['tanggal_cutoff_special']);

                $currMonth = !empty($request->getParam('month')) ? $request->getParam('month') : '';
                if(!empty($tanggalCutoffSpecial['bulan']) AND $tanggalCutoffSpecial['bulan'] == (int)$currMonth) {
                    $tanggalAwal = !empty($tanggalCutoffSpecial['tanggalAwal']) ? ((int)$tanggalCutoffSpecial['tanggalAwal']) : 1;
                    $tanggalAkhir = !empty($tanggalCutoffSpecial['tanggalAkhir']) ? ((int)$tanggalCutoffSpecial['tanggalAkhir']) : 1;
                    $dateStart = $year . '-' . $month . '-' . ($tanggalAwal < 10 ? ('0' . $tanggalAwal) : $tanggalAwal);
                    $dateEnd = $this->generateNextDate($dateStart, $tanggalAkhir);

                    $this->data['data'][$key]['setting']['tanggal_cutoff'] = $tanggalAwal;
                } else {
                    $dateStart = $year . '-' . $month . '-' . ($this->data['data'][$key]['setting']['tanggal_cutoff'] < 10 ? ('0' . $this->data['data'][$key]['setting']['tanggal_cutoff']) : $this->data['data'][$key]['setting']['tanggal_cutoff']);
                    $dateEnd = $this->generateNextDate($dateStart);
                }

                //echo $dateStart . '#' . $dateEnd; exit();

                $crDateStart = date_create($dateStart);
                $crDateEnd = date_create($dateEnd);
                $diff = date_diff($crDateEnd, $crDateStart);
                $this->data['data'][$key]['totalDay'] = $diff->format("%a");
                $this->data['data'][$key]['endDay'] = date('t', strtotime($dateStart));

                $this->data['data'][$key]['dataEmpHasSchedule'] = $this->getEmployeeSchedule($empCode, $dateStart, $dateEnd);
                $this->data['data'][$key]['dataEmpAbsence'] = $this->getEmployeeTransaksi($empCode, $dateStart, $dateEnd);
                $this->data['data'][$key]['dataEmpHasCuti'] = $this->getEmployeeCuti($value->emp_id, $dateStart, $dateEnd);
            }
        }

        $this->data['isExcel'] = $isExcel = !empty($request->getParam('excel')) ? $request->getParam('excel') : '';
        if(!empty($isExcel)) {
            $filename = 'report_' . str_replace('-', '', date('Y-m-d')) . '.xls';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename = " . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
        }

        $viewSchedule = !empty($request->getParam('schedule')) ? $request->getParam('schedule') : '';
        if(!empty($viewSchedule)) {
            return $this->ci->get('renderer')->render($response, 'report/absence/list_schedule.phtml', $this->data);
        } else {
            return $this->ci->get('renderer')->render($response, 'report/absence/list.phtml', $this->data);
        }
    }

    public function listsSchedule($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/absence-schedule' route");

        $arrData = array(
            'data' => array()
        );

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 0;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;
        $search = !empty($request->getParam('search')) ? $request->getParam('search') : '';

        $month = !empty($request->getParam('month')) ? $request->getParam('month') : '';
        $year = !empty($request->getParam('year')) ? $request->getParam('year') : date('Y');
        $bagianId = !empty($request->getParam('bagianId')) ? $request->getParam('bagianId') : '';
        $unitId = !empty($request->getParam('unitId')) ? $request->getParam('unitId') : '';
        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : '';

        $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->data['myRoleAccess'])) ? true : false;
        $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->data['myRoleAccess'])) ? true : false;

        $arrUnitId = [];
        if ($onlyUnit) {
            $res = Employee::getAllUnit($_SESSION['EMPID']);
            foreach ($res as $key => $value) {
                if (!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
                $unitId = $value->uni_id;
                $bagianId = $value->bag_id;
            }
            if (empty($arrUnitId)) $arrUnitId[0] = 123456789;
        }

        $arrDivisiId = [];
        if ($onlyDivisi) {
            $res = Employee::getAllDivisi($_SESSION['EMPID']);
            foreach ($res as $key => $value) {
                if (!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
                $bagianId = $value->bag_id;
                $unitId = $value->uni_id;
            }
            if (empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
        }

        if (($_SESSION['USERID'] == 1 AND !empty($bagianId)) OR (!empty($bagianId) AND !$onlyDivisi)) {
            $arrDivisiId = [];
            $arrDivisiId[0] = $bagianId;
        }
        if (($_SESSION['USERID'] == 1 AND !empty($unitId)) OR (!empty($unitId) AND !$onlyUnit)) {
            $arrUnitId = [];
            $arrUnitId[0] = $unitId;
        }

        if ($_SESSION['USERID'] != 1 AND $onlyDivisi AND !$onlyUnit) $arrUnitId = [];
        if ($_SESSION['USERID'] != 1 AND $onlyUnit AND !$onlyDivisi) $arrDivisiId = [];

        $this->data['onlyDivisi'] = $onlyDivisi;
        $this->data['onlyUnit'] = $onlyUnit;

        // print_r($arrDivisiId);
        // print_r($arrUnitId);
        // exit();

        //echo $_SESSION['USERID'];


        $resultTotal = Employee::getAllNonVoid('', '', $search, $arrUnitId, $arrDivisiId, $empId);
        $result = Employee::getAllNonVoid($limit, $offset, $search, $arrUnitId, $arrDivisiId, $empId);
        if (!empty($result)) {
            $arrSchedule = Schedule::getOptNonVoid();

            $this->data['recordsTotal'] = count($resultTotal);
            $this->data['recordsFiltered'] = count($resultTotal);
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
                $this->data['data'][$key] = array(
                    ($key + 1),
                    $value->emp_id,
                    $value->emp_code,
                    $value->emp_name,
                );
                $len = count($this->data['data'][$key]);
                $forLimit = $jumlahTanggal + $len;
                //echo $forLimit; exit();

                $cnt = 1;
                for ($i = $len; $i < $forLimit; $i++) {
                    $tanggal = $cnt < 10 ? ('0' . $cnt) : $cnt;
                    $generateId = $year . $month . $tanggal . $value->emp_id;
                    $scheduleDate = $year . '-' . $month . '-' . $tanggal;
                    $lblShift = !empty($dataEmpHasSchedule[$value->emp_code][$generateId]['code']) ? $dataEmpHasSchedule[$value->emp_code][$generateId]['code'] : $this->getLabelScheduleDefault($scheduleDate);
                    $this->data['data'][$key][$i] = $lblShift;
                    $cnt++;
                }
            }
            //print_r($this->data['data']);
        }

        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['bagian'] = Bagian::getBagianByID($bagianId);
        $this->data['arrMonthName'] = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $this->data['isExcel'] = $isExcel = !empty($request->getParam('excel')) ? $request->getParam('excel') : '';
        if(!empty($isExcel)) {
            $filename = 'report_' . str_replace('-', '', date('Y-m-d')) . '.xls';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename = " . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
        }

        return $this->ci->get('renderer')->render($response, 'report/absence/list_schedule.phtml', $this->data);
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

    public function listsYearly($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/absence/list' route");

        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : '';
        if(!empty($_SESSION['GUEST'])) $empId = $_SESSION['EMPID'];

        $year = !empty($request->getParam('year')) ? $request->getParam('year') : date('Y');

        $bagianId = !empty($request->getParam('bagianId')) ? $request->getParam('bagianId') : '';
        $unitId = !empty($request->getParam('unitId')) ? $request->getParam('unitId') : '';

        $arrEmpId = [];

        if (!empty($empId)) {
            $emp = new \stdClass();
            $emp->emp_id = $empId;
            $arrEmpId[0] = $emp;
        } else {
            if (!empty($bagianId)) {
                $arrEmpId = Employee::getEmployeeByBagian($bagianId);
            }
            if (!empty($unitId)) {
                $arrEmpId = Employee::getEmployeeByUnit($unitId);
            }

            $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->data['myRoleAccess'])) ? true : false;
            $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->data['myRoleAccess'])) ? true : false;

            $arrUnitId = [];
            $arrDivisiId = [];
            if ($onlyUnit) {
                $res = Employee::getAllUnit($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
                }
                if (empty($arrUnitId)) $arrUnitId[0] = 123456789;
                $arrEmpId = Employee::getEmployeeByUnit($arrUnitId);
            } elseif ($onlyDivisi) {
                $res = Employee::getAllDivisi($_SESSION['EMPID']);
                foreach ($res as $key => $value) {
                    if (!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
                }
                if (empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
                $arrEmpId = Employee::getEmployeeByBagian($arrDivisiId);
            }
        }

        $this->data['data'] = [];

        if (!empty($arrEmpId)) {
            foreach ($arrEmpId as $key => $value) {
                for ($i = 1; $i <= 12; $i++) {
                    $month = $i < 10 ? "0$i" : $i;
                    $this->data['data'][$key][$i]['month'] = $month;
                    $this->data['data'][$key][$i]['year'] = $year;
                    $this->data['data'][$key][$i]['arrDayName'] = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $this->data['data'][$key][$i]['arrMonthName'] = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $this->data['data'][$key][$i]['totalDay'] = date('t', mktime(0, 0, 0, $month, 1, $year));

                    $this->data['data'][$key][$i]['izinTidakHadir'] = Status::getAllKetidakhadiranNonVoid();
                    $this->data['data'][$key][$i]['cntIzinTidakHadir'] = count($this->data['data'][$key][$i]['izinTidakHadir']);
                    $this->data['data'][$key][$i]['employee'] = Employee::getEmployeeByID($value->emp_id);

                    $empCode = !empty($this->data['data'][$key][$i]['employee']->emp_code) ? $this->data['data'][$key][$i]['employee']->emp_code : '-';

                    $this->data['data'][$key][$i]['empId'] = $value->emp_id;
                    $this->data['data'][$key][$i]['empCode'] = $empCode;

                    $this->data['data'][$key][$i]['setting'] = $this->getSettingDb();

                    $tanggalCutoffSpecial = $this->getTanggalCutoffSpecial($this->data['data'][$key][$i]['setting']['tanggal_cutoff_special']);

                    $currMonth = !empty($request->getParam('month')) ? $request->getParam('month') : '';
                    if(!empty($tanggalCutoffSpecial['bulan']) AND $tanggalCutoffSpecial['bulan'] == (int)$currMonth) {
                        $tanggalAwal = !empty($tanggalCutoffSpecial['tanggalAwal']) ? ((int)$tanggalCutoffSpecial['tanggalAwal']) : 1;
                        $tanggalAkhir = !empty($tanggalCutoffSpecial['tanggalAkhir']) ? ((int)$tanggalCutoffSpecial['tanggalAkhir']) : 1;
                        $dateStart = $year . '-' . $month . '-' . ($tanggalAwal < 10 ? ('0' . $tanggalAwal) : $tanggalAwal);
                        $dateEnd = $this->generateNextDate($dateStart, $tanggalAkhir);

                        $this->data['data'][$key][$i]['setting']['tanggal_cutoff'] = $tanggalAwal;
                    } else {
                        $dateStart = $year . '-' . $month . '-' . ($this->data['data'][$key][$i]['setting']['tanggal_cutoff'] < 10 ? ('0' . $this->data['data'][$key][$i]['setting']['tanggal_cutoff']) : $this->data['data'][$key][$i]['setting']['tanggal_cutoff']);
                        $dateEnd = $this->generateNextDate($dateStart);
                    }

                    $crDateStart = date_create($dateStart);
                    $crDateEnd = date_create($dateEnd);
                    $diff = date_diff($crDateEnd, $crDateStart);
                    $this->data['data'][$key][$i]['totalDay'] = $diff->format("%a");
                    $this->data['data'][$key][$i]['endDay'] = date('t', strtotime($dateStart));

                    $this->data['data'][$key][$i]['dataEmpHasSchedule'] = $this->getEmployeeSchedule($empCode, $dateStart, $dateEnd);
                    $this->data['data'][$key][$i]['dataEmpAbsence'] = $this->getEmployeeTransaksi($empCode, $dateStart, $dateEnd);
                    $this->data['data'][$key][$i]['dataEmpHasCuti'] = $this->getEmployeeCuti($value->emp_id, $dateStart, $dateEnd);
                }
            }
        }

        $this->data['isExcel'] = $isExcel = !empty($request->getParam('excel')) ? $request->getParam('excel') : '';
        if(!empty($isExcel)) {
            $filename = 'report_' . str_replace('-', '', date('Y-m-d')) . '.xls';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename = " . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
        }

        return $this->ci->get('renderer')->render($response, 'report/absence/list_tahunan.phtml', $this->data);
    }

    private function getEmployeeSchedule($empId = '', $dateStart = '', $dateEnd = '')
    {
        $arrData = [
            'detail' => [],
            'izin' => [],
            'izinSanksi' => [],
            'izinName' => [],
            'shift' => [],
        ];
        $res = Employeeschedule::getAllNonVoidWhereIn(array($empId), $dateStart, $dateEnd);
        if (!empty($res)) {
            foreach ($res as $key => $value) {
                $minTime = strtotime($value->schd_waktu_awal);
                $maxTime = strtotime($value->schd_waktu_akhir);

                $tmp = date('h:i', ($maxTime - $minTime));

                list($hour, $minute) = explode(':', $tmp);
                $hour = (int)$hour;
                $minute = (int)$minute;
                $hour = $hour > 0 ? (($hour - 1) * 60) : 0;
                $minute += $hour;

                $arrData['detail'][$value->emsc_emp_id][$value->emsc_uniq_code] = [
                    'wkt_min' => $value->schd_waktu_awal,
                    'wkt_max' => $value->schd_waktu_akhir,
                    'code' => $value->schd_code,
                    'keterangan' => $value->emsc_status_reason,
                    'total_waktu' => $minute,
                    'isScheduleGantiHari' => (!empty($value->schd_ganti_hari) ? $value->schd_ganti_hari : 0),
                ];
                if (!empty($value->sta_id)) {
                    $arrData['izin'][$value->emsc_emp_id][$value->emsc_uniq_code][$value->sta_id] = $value->sta_id;
                    $arrData['izinSanksi'][$value->emsc_emp_id][$value->emsc_uniq_code][$value->sta_id] = !empty($value->sta_sanksi) ? $value->sta_sanksi : 0;
                    $arrData['izinName'][$value->emsc_emp_id][$value->emsc_uniq_code][$value->sta_id] = strtolower($value->sta_name);
                }

                if (!empty($value->schd_code)) $arrData['shift'][$value->schd_code] = $minute;
            }
        }
        return $arrData;
    }

    private function getEmployeeTransaksi($userCode = '-', $dateStart = '', $dateEnd = '')
    {
        $arrData = [];
        $res = Employeeschedule::getAllNonVoidWhereIn(array($userCode), $dateStart, $dateEnd);
        if (!empty($res)) {
            foreach ($res as $key => $value) {
                $totalWaktu = !empty($value->totalWaktu) ? $this->calculateTime($value->totalWaktu) : 0;
                $arrData[$value->tran_cardNo][$value->tgl] = [
                    'wkt_min' => $value->wkt_min == '0000-00-00 00:00:00' ? '' : $value->wkt_min,
                    'wkt_max' => $value->wkt_max == '0000-00-00 00:00:00' ? '' : $value->wkt_max,
                    'time_min' => $value->time_min == '00:00:00' ? '' : $value->time_min,
                    'time_max' => $value->time_max == '00:00:00' ? '' : $value->time_max,
                    'totalWaktu' => $totalWaktu,
                ];
            }
        }
        return $arrData;
    }

    private function getEmployeeCuti($empId = '', $dateStart = '', $dateEnd = '')
    {
        $arrData = [];
        $res = Izin::getAllNonVoidWhereIn(array($empId), $dateStart, $dateEnd);
        if (!empty($res)) {
            foreach ($res as $key => $value) {
                $uniqCode = str_replace('-', '', $value->emcu_tanggal_awal) . $value->emcu_emp_id;
                $arrData[$value->emcu_emp_id][$uniqCode][$value->sta_id] = [
                    'namaIzin' => $value->sta_name,
                    'keterangan' => $value->emcu_keterangan,
                ];
            }
        }
        return $arrData;
    }

    private function calculateTime($data = '')
    {
        if (empty($data)) return '';
        list($hour, $minute, $second) = explode(':', $data);
        $total = (int)$hour * 3600;
        $total += (int)$minute * 60;
        $total += (int)$second;
        $total = floor($total / 60);
        return $total;
    }

    private function generateNextDate($date, $tanggalSpecialAkhir = '')
    {
        list($year, $month, $day) = explode('-', $date);
        $nextDay = (int)$day - 1;
        $nextYear = $year;
        $nextMonth = (int)$month + 1;

        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear += 1;
        }

        //echo "string - " . $day;
        if ($day == '01') {
            $nextMonth = (int)$month;
            $nextDay = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
        }

        if ($nextDay < 1) $nextDay = (int)date('t', mktime(0, 0, 0, $nextMonth, 1, $nextYear));

        if(!empty($tanggalSpecialAkhir)) {
            $nextDay = $tanggalSpecialAkhir;
        }

        $nextDate = $nextYear . '-' . ($nextMonth < 10 ? ("0$nextMonth") : $nextMonth) . '-' . ($nextDay < 10 ? ("$nextDay") : $nextDay);
        // echo $nextDate; exit();
        return $nextDate;
    }

    public function form($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/bagian/list' route");

        $this->data['menuActived'] = 'report';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'] . '-' . $this->data['selectedMonth'] . '-01'));
        $this->data['listMonth'] = $this->getMonthFilter();

        $this->data['yearFilterRange'] = $this->getYearFilterRange();

        $onlyUnit = ($_SESSION['USERID'] != 1 AND in_array(17, $this->data['myRoleAccess'])) ? true : false;
        $onlyDivisi = ($_SESSION['USERID'] != 1 AND in_array(18, $this->data['myRoleAccess'])) ? true : false;

        $arrUnitId = [];
        if ($onlyUnit) {
            $res = Employee::getAllUnit($_SESSION['EMPID']);
            foreach ($res as $key => $value) {
                if (!empty($value->uni_id)) $arrUnitId[$value->uni_id] = $value->uni_id;
            }
            if (empty($arrUnitId)) $arrUnitId[0] = 123456789;
            // print_r($arrUnitId);
        }

        $arrDivisiId = [];
        if ($onlyDivisi) {
            $res = Employee::getAllDivisi($_SESSION['EMPID']);
            foreach ($res as $key => $value) {
                if (!empty($value->bag_id)) $arrDivisiId[$value->bag_id] = $value->bag_id;
            }
            if (empty($arrDivisiId)) $arrDivisiId[0] = 123456789;
            // print_r($arrDivisiId);
        }

        $this->data['optEmployee'] = Employee::getOptNonVoid($arrUnitId, $arrDivisiId);

        return $this->ci->get('renderer')->render($response, 'report/absence/form.phtml', $this->data);
    }

    public function formIndividual($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/bagian/list' route");

        $this->data['menuActived'] = 'report';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'] . '-' . $this->data['selectedMonth'] . '-01'));
        $this->data['listMonth'] = $this->getMonthFilter();

        $this->data['yearFilterRange'] = $this->getYearFilterRange();

        return $this->ci->get('renderer')->render($response, 'report/absence/form_individual.phtml', $this->data);
    }

    private function getLastMonth($month = '', $year = '') {
        $ret = [
            'month' => $month,
            'year' => $year,
        ];

        $month = (int)$month - 1;
        if($month == 0) {
            $ret['month'] = 12;
            $ret['year'] -= 1;
        } else {
            $ret['month'] = $month < 10 ? ('0' . $month) : $month;
        }

        return $ret;
    }

    public function summary($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/summary' route");

        $this->data['startDate'] = !empty($request->getParam('startDate')) ? $request->getParam('startDate') : (date('m') . '/01/' . date('Y'));
        $this->data['endDate'] = !empty($request->getParam('endDate')) ? $request->getParam('endDate') : date('m/t/Y');

        $this->data['menuActived'] = 'report';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['status'] = Status::getAllKetidakhadiranNonVoid('sta_name');

        return $this->ci->get('renderer')->render($response, 'report/absence/summary.phtml', $this->data);
    }

    private function getTanggalCutoffSpecial($val = '') {
        $arr = [
            'bulan' => '',
            'tanggalAwal' => '',
            'tanggalAkhir' => '',
        ];

        if(!empty($val)) {
            $tmp = explode(':', $val);
            if(!empty($tmp)) {
                $arr['bulan'] = $tmp[0];
                if(!empty($tmp[1])) {
                    $tmp2 = explode('#', $tmp[1]);
                    if(!empty($tmp2)) {
                        $arr['tanggalAwal'] = !empty($tmp2[0]) ? $tmp2[0] : '';
                        $arr['tanggalAkhir'] = !empty($tmp2[1]) ? $tmp2[1] : '';
                    }
                }
            }
        }

        //echo print_r($arr); exit();

        return $arr;
    }
}
