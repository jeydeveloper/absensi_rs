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
      $patient = IzinModel::where('emcu_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = IzinModel::where('emcu_id', $id)->first();
      return $patient;
    }
}
