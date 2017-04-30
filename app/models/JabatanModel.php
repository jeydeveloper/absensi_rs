<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    protected $table      = 'jabatan';   // it's always better to specify it
    protected $primaryKey = 'jab_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return JabatanModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = JabatanModel::where('jab_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = JabatanModel::where('jab_id', $id)->first();
      return $patient;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = JabatanModel::where('jab_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->jab_id,
            'value' => $value->jab_name,
          );
        }
      }

      return $arrData;
    }
}
