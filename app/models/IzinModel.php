<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinModel extends Model
{
    protected $table      = 'employee_cuti';   // it's always better to specify it
    protected $primaryKey = 'emcu_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return IzinModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = IzinModel::join('employee', 'emcu_emp_id', '=', 'emp_id')->leftjoin('status', 'emcu_sta_id', '=', 'sta_id')->where('emcu_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = IzinModel::where('emcu_id', $id)->first();
      return $patient;
    }

    public static function getAllNonVoidWhereIn($data = null)
    {
      if(!is_array($data)) return 0;

      $res = IzinModel::join('employee', 'emcu_emp_id', '=', 'emp_id')->leftjoin('status', 'emcu_sta_id', '=', 'sta_id')->where('emcu_void', 0)->whereIn('emcu_emp_id', $data)->get();
      return $res;
    }
}
