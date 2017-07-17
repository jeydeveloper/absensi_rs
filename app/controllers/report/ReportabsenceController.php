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

class ReportabsenceController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /report/absence/list' route");

        $empId = !empty($request->getParam('empId')) ? $request->getParam('empId') : 0;
        $month = !empty($request->getParam('month')) ? $request->getParam('month') : date('m');
        $year = !empty($request->getParam('year')) ? $request->getParam('year') : date('Y');

        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['arrDayName'] = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $this->data['arrMonthName'] = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $this->data['totalDay'] = date('t', mktime(0, 0, 0, $month, 1, $year));

        $this->data['izinTidakHadir'] = Status::getAllKetidakhadiranNonVoid();
        $this->data['cntIzinTidakHadir'] = count($this->data['izinTidakHadir']);
        $this->data['employee'] = Employee::getEmployeeByID($empId);

        $empCode = !empty($this->data['employee']->emp_code) ? $this->data['employee']->emp_code : '-';

        $this->data['empId'] = $empId;
        $this->data['empCode'] = $empCode;

        $dateStart = $year . '-' . $month . '-01';
        $dateEnd = $year . '-' . $month . '-' . date('t', strtotime($dateStart));
        $this->data['dataEmpHasSchedule'] = $this->getEmployeeSchedule($empId, $dateStart, $dateEnd);
        $this->data['dataEmpAbsence'] = $this->getEmployeeTransaksi($empCode, $dateStart, $dateEnd);
        $this->data['dataEmpHasCuti'] = $this->getEmployeeCuti($empId, $dateStart, $dateEnd);
        // print_r($this->data['dataEmpHasSchedule']);
        // print_r($this->data['dataEmpAbsence']);
        // print_r($this->data['dataEmpHasCuti']);

        $this->data['setting'] = $this->getSettingDb();

        return $this->ci->get('renderer')->render($response, 'report/absence/list.phtml', $this->data);
    }

    private function getEmployeeSchedule($empId = '', $dateStart = '', $dateEnd = '') {
      $arrData = [
        'detail' => [],
        'izin' => [],
      ];
      $res = Employeeschedule::getAllNonVoidWhereIn(array($empId), $dateStart, $dateEnd);
      if(!empty($res)) {
        foreach ($res as $key => $value) {
          $arrData['detail'][$value->emsc_emp_id][$value->emsc_uniq_code] = [
            'wkt_min' => $value->schd_waktu_awal,
            'wkt_max' => $value->schd_waktu_akhir,
            'code' => $value->schd_code,
          ];
          if(!empty($value->sta_id)) $arrData['izin'][$value->emsc_emp_id][$value->emsc_uniq_code][$value->sta_id] = 1;
        }
      }
      return $arrData;
    }

    private function getEmployeeTransaksi($userCode = '-', $dateStart = '', $dateEnd = '') {
      $arrData = [];
      $res = Transaksi::getAllMinMaxTranTime($dateStart, $dateEnd, array($userCode));
      if(!empty($res)) {
        foreach ($res as $key => $value) {
          $totalWaktu = !empty($value->totalWaktu) ? $this->calculateTime($value->totalWaktu) : 0;
          $arrData[$value->tran_cardNo][$value->tgl] = [
            'wkt_min' => $value->wkt_min,
            'wkt_max' => $value->wkt_max,
            'time_min' => $value->time_min,
            'time_max' => $value->time_max,
            'totalWaktu' => $totalWaktu,
          ];
        }
      }
      return $arrData;
    }

    private function getEmployeeCuti($empId = '', $dateStart = '', $dateEnd = '') {
      $arrData = [];
      $res = Izin::getAllNonVoidWhereIn(array($empId), $dateStart, $dateEnd);
      if(!empty($res)) {
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

    private function calculateTime($data = '') {
      if(empty($data)) return '';
      list($hour, $minute, $second) = explode(':', $data);
      $total = (int)$hour * 3600;
      $total += (int)$minute * 60;
      $total += (int)$second;
      $total = floor($total/60);
      return $total;
    }
}
