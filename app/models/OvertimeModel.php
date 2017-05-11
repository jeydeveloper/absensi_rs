<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeModel extends Model
{
    protected $table      = 'employee_overtime';   // it's always better to specify it
    protected $primaryKey = 'emov_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return OvertimeModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = OvertimeModel::where('emov_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = OvertimeModel::where('emov_id', $id)->first();
      return $patient;
    }
}
