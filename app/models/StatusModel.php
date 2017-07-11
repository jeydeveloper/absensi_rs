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
      $res = StatusModel::where('sta_void', 0)->get();
      return $res;
    }

    public static function getAllKehadiranNonVoid()
    {
      $res = StatusModel::where('sta_void', 0)->where('sta_type', 1)->get();
      return $res;
    }

    public static function getAllKetidakhadiranNonVoid()
    {
      $res = StatusModel::where('sta_void', 0)->where('sta_type', 2)->get();
      return $res;
    }

    public static function getPatientByID($id)
    {
      $res = StatusModel::where('sta_id', $id)->first();
      return $res;
    }

    public static function getOptKetidakhadiranNonVoid()
    {
      $arrData = array();

      $result = StatusModel::where('sta_void', 0)->where('sta_type', 2)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->sta_id,
            'value' => $value->sta_name,
          );
        }
      }

      return $arrData;
    }
}
