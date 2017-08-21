<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UseradminModel as Useradmin;

class EmployeescheduleModel extends Model
{
    protected $table      = 'employee_schedule';   // it's always better to specify it
    protected $primaryKey = 'emsc_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

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
      if(!is_array($data)) return 0;

      if (!empty($dateStart)) {
        $dateEnd = !empty($dateEnd) ? $dateEnd : $dateStart;
        $res = EmployeescheduleModel::join('schedule', 'emsc_schd_id', '=', 'schd_id')
        ->leftjoin('status', 'emsc_sta_id', '=', 'sta_id')
        ->where('emsc_void', 0)
        ->whereIn('emsc_emp_id', $data)
        ->whereRaw('emsc_date BETWEEN "'.$dateStart.'" AND "'.$dateEnd.'"')
        ->get();
      } else {
        $res = EmployeescheduleModel::join('schedule', 'emsc_schd_id', '=', 'schd_id')->leftjoin('status', 'emsc_sta_id', '=', 'sta_id')->where('emsc_void', 0)->whereIn('emsc_emp_id', $data)->get();
      }

      return $res;
    }

    public static function getByUniqCode($uniqCode)
    {
      $res = EmployeescheduleModel::leftjoin('status', 'emsc_sta_id', '=', 'sta_id')->where('emsc_uniq_code', $uniqCode)->first();
      return $res;
    }

    private function getRoleAccess() {
      $arrData = [];
      $res = Useradmin::getUserByID($_SESSION['USERID']);
      if(!empty($res) AND !empty($res->role_privilege)) {
        $arrData = explode(',', $res->role_privilege);
      }
      return $arrData;
    }
}
