<?php

namespace App\Controllers\Proses;

use Interop\Container\ContainerInterface;
use Gettext\Translator;
use App\Models\ScheduleModel as Schedule;
use App\Models\TransaksiModel as Transaksi;
use App\Models\EmployeeModel as Employee;
use App\Models\SettingModel as Setting;
use App\Models\User as User;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Collection;

class MappingjadwalController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'sta_id';
        $this->data['inputFocus'] = 'sta_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $this->data['myRoleAccess'] = $this->getRoleAccess($_SESSION['USERID']);
        $this->data['isTutupJadwal'] = $this->checkTutupJadwal();
    }

    public function lists($request, $response, $args)
    {
        $setting = $this->getSettingDb();
        $tglTutupJadwal = !empty($setting['tanggal_tutup_jadwal']) ? $setting['tanggal_tutup_jadwal'] : '';
        $res = User::getUserByID($_SESSION['USERID']);
        if($res->usr_username != 'superadmin') {
            if($tglTutupJadwal == (int)date('d')) {
                return $response->withRedirect($this->ci->get('settings')['baseUrl']);
                exit();
            }
        }

        //print_r($_GET); exit();

        $this->ci->get('logger')->info("Slim-Skeleton 'GET /proses/mappingjadwal/list' route");

        $this->data['selectedMonth'] = !empty($request->getParam('slMonth')) ? $request->getParam('slMonth') : date('m');
        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');

        $this->data['empId'] = !empty($request->getParam('empId')) ? $request->getParam('empId') : 0;

        if (!empty($request->getParam('import'))) {
            $this->doImportData($this->data['selectedMonth'], $this->data['selectedYear'], $this->data['empId']);
            return $response->withRedirect($this->ci->get('settings')['baseUrl'] . 'mapping-jadwal?slMonth='.$this->data['selectedMonth'].'&slYear='.$this->data['selectedYear'].'&empId='.$this->data['empId']);
            exit();
        }

        if (!empty($request->getParam('importDefault'))) {
            $this->doImportDataDefault($this->data['selectedMonth'], $this->data['selectedYear'], $this->data['empId']);
            return $response->withRedirect($this->ci->get('settings')['baseUrl'] . 'mapping-jadwal?slMonth='.$this->data['selectedMonth'].'&slYear='.$this->data['selectedYear'].'&empId='.$this->data['empId']);
            exit();
        }

        $this->data['totalDay'] = date('t', strtotime($this->data['selectedYear'] . '-' . $this->data['selectedMonth'] . '-01'));
        $this->data['listMonth'] = $this->getMonthFilter();

        $this->data['listSchedule'] = Schedule::getAllNonVoid();

        $this->data['menuActived'] = 'prosesAbsensi';
        $this->data['sideMenu'] = $this->ci->get('renderer')->fetch('sidemenu.phtml', $this->data);

        $this->data['selectedYear'] = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');
        $this->data['yearFilterRange'] = $this->getYearFilterRange();

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

        $this->data['optEmployee'] = Employee::getOptNonVoid($arrUnitId, $arrDivisiId);

        return $this->ci->get('renderer')->render($response, 'proses/mappingjadwal/list.phtml', $this->data);
    }

    private function doImportDataDefault($month = '', $year = '', $empId = '')
    {
        $dateStart = "$year-$month-01";
        $res = Employee::getAllNonVoid('', '', '', [], [], $empId);
        if ($res->count()) {
            $this->bulkInsertScheduleDefault($res, $dateStart);
        }
    }

    private function doImportData($month = '', $year = '', $empId = '')
    {
        $dateStart = "$year-$month-01";
        $dateEnd = date('Y-m-t', strtotime($dateStart));

        if(!empty($empId)) {
            $dataEmp = [];
            $res = Employee::getAllNonVoid('', '', '', [], [], $empId);
            if(!empty($res)) {
                foreach ($res as $key => $value) {
                    $dataEmp[$value->emp_code] = $value->emp_code;
                }
            }
        } else {
            $dataEmp = [];
        }
        $res = Transaksi::getAllMinMaxTranTime($dateStart, $dateEnd, $dataEmp);
        if ($res->count()) {
            $this->bulkInsert($res);
        }
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

    private function bulkInsert($data = null)
    {
        if (empty($data)) return false;

        $current = date('Y-m-d H:i:s');
        $inserts = [];
        foreach ($data as $key => $value) {
            //if($key > 2) break;
            list($y, $m, $d) = explode('-', $value->tgl);
            $dayNo = date('w', mktime(0, 0, 0, $m, $d, $y));
            $flagWeekend = 1;
            if (!in_array($dayNo, [6, 0])) {
                $flagWeekend = 0;
            }

            //$uniqCode = str_replace('-', '', $value->tgl) . $value->tran_cardNo;
            $uniqCode = str_replace('-', '', $value->tgl) . $value->emp_id;

            $inserts[] = implode(', ', [
                '"' . $uniqCode . '"',
                '"' . $value->tran_cardNo . '"',
                '"' . $value->emp_id . '"',
                '"' . $value->wkt_min . '"',
                '"' . $value->wkt_max . '"',
                '"' . $value->totalWaktu . '"',
                '"' . $value->tgl . '"',
                '"' . $current . '"',
                '"' . $current . '"',
                '"' . $flagWeekend . '"'
            ]);
        }

        $inserts = Collection::make($inserts);

        $inserts->chunk(500)->each(function ($ch) {
            $insertString = '';
            foreach ($ch as $element) {
                $insertString .= '(' . $element . '), ';
            }

            $insertString = rtrim($insertString, ", ");

            try {
                DB::insert("INSERT INTO abs_employee_schedule (
                `emsc_uniq_code`,
                `emsc_emp_code`,
                `emsc_emp_id`,
                `emsc_real_date_start`,
                `emsc_real_date_end`,
                `emsc_real_total_waktu`,
                `emsc_date`,
                `emsc_created_at`,
                `emsc_updated_at`,
                `emsc_flag_weekend`
                ) VALUES $insertString ON DUPLICATE KEY UPDATE 
                `emsc_uniq_code` = VALUES(`emsc_uniq_code`),
                `emsc_emp_code` = VALUES(`emsc_emp_code`),
                `emsc_emp_id` = VALUES(`emsc_emp_id`),
                `emsc_real_date_start` = VALUES(`emsc_real_date_start`),
                `emsc_real_date_end` = VALUES(`emsc_real_date_end`),
                `emsc_real_total_waktu` = VALUES(`emsc_real_total_waktu`),
                `emsc_date` = VALUES(`emsc_date`),
                `emsc_created_at` = VALUES(`emsc_created_at`),
                `emsc_updated_at` = VALUES(`emsc_updated_at`),
                `emsc_flag_weekend` = VALUES(`emsc_flag_weekend`)
                ");
            } catch (\Exception $e) {
                print_r([$e->getMessage()]);
            }
        });

        /*$res = DB::statement('SELECT * FROM abs_holiday');
        var_dump($res);
        echo 'test';*/
    }

    private function bulkInsertScheduleDefault($data = null, $dateStart = '')
    {
        if (empty($data)) return false;

        list($y, $m) = explode('-', $dateStart);
        $setting = $this->getSettingDb();
        $current = date('Y-m-d H:i:s');
        $inserts = [];
        foreach ($data as $key => $value) {
            $tanggalAkhir = date('t', strtotime($dateStart));
            for ($i = 1; $i <= $tanggalAkhir; $i++) {
                $d = $i < 10 ? ("0$i") : $i;
                $tgl = "$y-$m-$d";
                $dayNo = date('w', mktime(0, 0, 0, $m, $d, $y));
                $defaultScheduleCode = '';
                $defaultScheduleDateStart = '';
                $defaultScheduleDateEnd = '';
                $flagWeekend = 1;
                if (!in_array($dayNo, [6, 0])) {
                    $defaultScheduleCode = $dayNo == 5 ? $setting['default_shift_2'] : $setting['default_shift_1'];
                    $schedule = Schedule::where('schd_code', $defaultScheduleCode)->first();
                    if (!empty($schedule)) {
                        $defaultScheduleDateStart = "{$tgl} {$schedule->schd_waktu_awal}";
                        $defaultScheduleDateEnd = "{$tgl} {$schedule->schd_waktu_akhir}";
                    }
                    $flagWeekend = 0;
                }

                $uniqCode = str_replace('-', '', $tgl) . $value->emp_code;

                $inserts[] = implode(', ', [
                    '"' . $uniqCode . '"',
                    '"' . $value->emp_code . '"',
                    '"' . $value->emp_id . '"',
                    '"' . $defaultScheduleCode . '"',
                    '"' . $defaultScheduleDateStart . '"',
                    '"' . $defaultScheduleDateEnd . '"',
                    '"' . $tgl . '"',
                    '"' . $current . '"',
                    '"' . $current . '"',
                    '"' . $flagWeekend . '"'
                ]);
            }
        }

        $inserts = Collection::make($inserts);

        $inserts->chunk(500)->each(function ($ch) {
            $insertString = '';
            foreach ($ch as $element) {
                $insertString .= '(' . $element . '), ';
            }

            $insertString = rtrim($insertString, ", ");

            try {
                DB::insert("INSERT INTO abs_employee_schedule (
                `emsc_uniq_code`,
                `emsc_emp_code`,
                `emsc_emp_id`,
                `emsc_default_schd_code`,
                `emsc_default_date_start`,
                `emsc_default_date_end`,
                `emsc_date`,
                `emsc_created_at`,
                `emsc_updated_at`,
                `emsc_flag_weekend`
                ) VALUES $insertString ON DUPLICATE KEY UPDATE 
                `emsc_uniq_code` = VALUES(`emsc_uniq_code`),
                `emsc_emp_code` = VALUES(`emsc_emp_code`),
                `emsc_emp_id` = VALUES(`emsc_emp_id`),
                `emsc_default_schd_code` = VALUES(`emsc_default_schd_code`),
                `emsc_default_date_start` = VALUES(`emsc_default_date_start`),
                `emsc_default_date_end` = VALUES(`emsc_default_date_end`),
                `emsc_date` = VALUES(`emsc_date`),
                `emsc_created_at` = VALUES(`emsc_created_at`),
                `emsc_updated_at` = VALUES(`emsc_updated_at`),
                `emsc_flag_weekend` = VALUES(`emsc_flag_weekend`)
                ");
            } catch (\Exception $e) {
                print_r([$e->getMessage()]);
            }
        });
    }
}
