<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleaccessModel extends Model
{
    protected $table      = 'role_access';   // it's always better to specify it
    protected $primaryKey = 'role_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return RoleaccessModel::all();
    }

    public static function getAllNonVoid()
    {
      $result = RoleaccessModel::where('role_void', 0)->get();
      return $result;
    }

    public static function getPatientByID($id)
    {
      $result = RoleaccessModel::where('role_id', $id)->first();
      return $result;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = RoleaccessModel::where('role_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->role_id,
            'value' => $value->role_name,
          );
        }
      }

      return $arrData;
    }
}
