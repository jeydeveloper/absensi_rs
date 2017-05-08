<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayModel extends Model
{
    protected $table      = 'holiday';   // it's always better to specify it
    protected $primaryKey = 'hol_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return HolidayModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = HolidayModel::where('hol_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = HolidayModel::where('hol_id', $id)->first();
      return $patient;
    }
}
