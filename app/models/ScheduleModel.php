<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleModel extends Model
{
    protected $table      = 'schedule';   // it's always better to specify it
    protected $primaryKey = 'schd_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return ScheduleModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = ScheduleModel::where('schd_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = ScheduleModel::where('schd_id', $id)->first();
      return $patient;
    }
}
