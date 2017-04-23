<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
    protected $table      = 'status';   // it's always better to specify it
    protected $primaryKey = 'sta_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return StatusModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = StatusModel::where('sta_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = StatusModel::where('sta_id', $id)->first();
      return $patient;
    }
}
