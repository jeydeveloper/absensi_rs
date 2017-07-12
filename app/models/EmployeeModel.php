<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeModel extends Model
{
    protected $table      = 'employee';   // it's always better to specify it
    protected $primaryKey = 'emp_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return EmployeeModel::all();
    }

    public static function getAllNonVoid($limit = 0, $offset = 0)
    {
      if(!empty($limit)) {
        // echo "string - " . $offset; exit();
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->limit($limit)->offset($offset)->get();
      } else {
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->get();
      }
      return $res;
    }

    public static function getEmployeeByID($id)
    {
      $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_id', $id)->first();
      return $res;
    }

    public static function getOptNonVoid()
    {
      $arrData = array();

      $result = EmployeeModel::where('emp_void', 0)->get();

      if(!empty($result)) {
        foreach ($result as $key => $value) {
          $arrData[] = array(
            'key' => $value->emp_id,
            'value' => $value->emp_name,
          );
        }
      }

      return $arrData;
    }
}
