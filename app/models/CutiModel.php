<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiModel extends Model
{
    protected $table      = 'cuti';   // it's always better to specify it
    protected $primaryKey = 'cut_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return CutiModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = CutiModel::where('cut_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = CutiModel::where('cut_id', $id)->first();
      return $patient;
    }
}
