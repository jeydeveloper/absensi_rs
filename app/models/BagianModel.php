<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BagianModel extends Model
{
    protected $table      = 'bagian';   // it's always better to specify it
    protected $primaryKey = 'bag_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return BagianModel::all();
    }

    public static function getAllNonVoid()
    {
      $result = BagianModel::where('bag_void', 0)->get();
      return $result;
    }

    public static function getPatientByID($id)
    {
      $result = BagianModel::where('bag_id', $id)->first();
      return $result;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = BagianModel::where('bag_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->bag_id,
            'value' => $value->bag_name,
          );
        }
      }

      return $arrData;
    }
}
