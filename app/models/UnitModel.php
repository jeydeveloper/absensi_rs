<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model
{
    protected $table      = 'unit';   // it's always better to specify it
    protected $primaryKey = 'uni_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return UnitModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = UnitModel::where('uni_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = UnitModel::where('uni_id', $id)->first();
      return $patient;
    }
}
