<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientModel extends Model
{
    protected $table      = 'patients';   // it's always better to specify it
    protected $primaryKey = 'ptn_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return PatientModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = PatientModel::where('ptn_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = PatientModel::where('ptn_id', $id)->first();
      return $patient;
    }

    public static function getPatientByCode($code)
    {
      $patient = PatientModel::where('ptn_code', $code)->where('ptn_void', 0)->first();
      return (isset($patient->ptn_id) ? $patient->ptn_id : 0);
    }
}
