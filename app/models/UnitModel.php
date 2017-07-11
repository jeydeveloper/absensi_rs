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
      $res = UnitModel::join('bagian', 'uni_bag_id', '=', 'bag_id')->where('uni_void', 0)->get();
      return $res;
    }

    public static function getUnitByID($id)
    {
      $res = UnitModel::where('uni_id', $id)->first();
      return $res;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = UnitModel::join('bagian', 'uni_bag_id', '=', 'bag_id')->where('uni_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData['bagian'][$value->uni_bag_id] = $value->bag_name;
          $arrData['unit'][$value->uni_bag_id][$value->uni_id] = array(
            'key' => $value->uni_id,
            'value' => $value->uni_name,
          );
        }
      }

      return $arrData;
    }
}
