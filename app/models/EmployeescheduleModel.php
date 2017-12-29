<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UseradminModel as Useradmin;

class EmployeescheduleModel extends Model
{
    protected $table = 'employee_schedule';   // it's always better to specify it
    protected $primaryKey = 'emsc_id';     // must be defined if different from 'id'
    public $timestamps = false;     // to get rid of created_at and updated_at
    protected $fillable = [
        'emsc_uniq_code',
        'emsc_emp_code',
        'emsc_schd_id',
        'emsc_real_date_start',
        'emsc_real_date_end',
        'emsc_real_total_waktu',
        'emsc_date',
        'emsc_created_at',
        'emsc_updated_at',
        'emsc_default_schd_code',
        'emsc_default_date_start',
        'emsc_default_date_end',
        'emsc_real_next_date',
        'emsc_emp_id',
    ];

    public function __construct()
    {
        // $this->myRoleAccess = $this->getRoleAccess();
    }

    public static function getAll()
    {
        return EmployeescheduleModel::all();
    }

    public static function getAllNonVoid()
    {
        $res = EmployeescheduleModel::where('emsc_void', 0)->get();
        return $res;
    }

    public static function getAllNonVoidWhereIn($data = null, $dateStart = '', $dateEnd = '')
    {
        if (!is_array($data)) return 0;

        //print_r($data);

        $dateStart = !empty($dateStart) ? $dateStart : date('Y-m-01');
        $dateEnd = !empty($dateEnd) ? $dateEnd : date('Y-m-t');

        list($year, $month, $day) = explode('-', $dateEnd);
        $lastDate = date('t', strtotime($dateEnd));
        if ($day == $lastDate) {
            $newMonth = 1 + (int)$month;
            if ($newMonth > 12) {
                $newMonth = 1;
                $year += 1;
            }
            $newMonth = $newMonth < 10 ? "0$newMonth" : $newMonth;
            $dateEnd = $year . '-' . $newMonth . '-01';
        } else {
            $newTanggal = 1 + (int)$day;
            $newTanggal = $newTanggal < 10 ? "0$newTanggal" : $newTanggal;
            $dateEnd = $year . '-' . $month . '-' . $newTanggal;
        }

        if (!empty($dateStart)) {
            $dateEnd = !empty($dateEnd) ? $dateEnd : $dateStart;
            $res = EmployeescheduleModel::leftjoin('schedule', 'emsc_schd_id', '=', 'schd_id')
                ->selectRaw("
                emsc_emp_code,
                emsc_uniq_code,
                emsc_schd_id,
                schd_waktu_awal,
                schd_waktu_akhir,
                schd_code,
                schd_color,
                sta_id,
                sta_name,
                emsc_status_reason,
                schd_ganti_hari,
                emsc_emp_id as emp_id, 
                emsc_emp_id, 
                emsc_emp_code as tran_cardNo, 
                emsc_real_date_start as wkt_min, 
                emsc_real_date_end as wkt_max, 
                time(emsc_real_date_start) as time_min, 
                time(emsc_real_date_end) as time_max, 
                emsc_date as tgl, 
                TIMEDIFF(emsc_real_date_end, emsc_real_date_start) as totalWaktu
                ")
                ->leftjoin('status', 'emsc_sta_id', '=', 'sta_id')
                ->where('emsc_void', 0)
                ->whereIn('emsc_emp_code', $data)
                ->whereRaw('emsc_date BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"')
                ->get();
        } else {
            $dateEnd = !empty($dateEnd) ? $dateEnd : $dateStart;
            $res = EmployeescheduleModel::leftjoin('schedule', 'emsc_schd_id', '=', 'schd_id')
                ->selectRaw("
                emsc_emp_code,
                emsc_uniq_code,
                emsc_schd_id,
                schd_waktu_awal,
                schd_waktu_akhir,
                schd_code,
                schd_color,
                sta_id,
                sta_name,
                emsc_status_reason,
                schd_ganti_hari,
                emsc_emp_id as emp_id, 
                emsc_emp_id, 
                emsc_emp_code as tran_cardNo, 
                emsc_real_date_start as wkt_min, 
                emsc_real_date_end as wkt_max, 
                time(emsc_real_date_start) as time_min, 
                time(emsc_real_date_end) as time_max, 
                emsc_date as tgl, 
                TIMEDIFF(emsc_real_date_end, emsc_real_date_start) as totalWaktu
                ")
                ->leftjoin('status', 'emsc_sta_id', '=', 'sta_id')
                ->where('emsc_void', 0)
                ->whereRaw('emsc_date BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"')
                ->get();
        }

        return $res;
    }

    public static function getByUniqCode($uniqCode)
    {
        $res = EmployeescheduleModel::leftjoin('status', 'emsc_sta_id', '=', 'sta_id')->where('emsc_uniq_code', $uniqCode)->first();
        return $res;
    }

    private function getRoleAccess()
    {
        $arrData = [];
        $res = Useradmin::getUserByID($_SESSION['USERID']);
        if (!empty($res) AND !empty($res->role_privilege)) {
            $arrData = explode(',', $res->role_privilege);
        }
        return $arrData;
    }
}
