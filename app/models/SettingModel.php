<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    protected $table      = 'setting';   // it's always better to specify it
    protected $primaryKey = 'sett_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return SettingModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = SettingModel::where('sett_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = SettingModel::where('sett_id', $id)->first();
      return $patient;
    }
}
