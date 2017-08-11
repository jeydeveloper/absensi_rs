<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roleaccess extends Model
{
    protected $table      = 'role_access';   // it's always better to specify it
    protected $primaryKey = 'rlac_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return Roleaccess::all();
    }

    public static function getAllNonVoid()
    {
      $result = Roleaccess::where('rlac_void', 0)->get();
      return $result;
    }

    public static function getPatientByID($id)
    {
      $result = Roleaccess::where('rlac_id', $id)->first();
      return $result;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = Roleaccess::where('rlac_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->rlac_id,
            'value' => $value->rlac_name,
          );
        }
      }

      return $arrData;
    }
}
