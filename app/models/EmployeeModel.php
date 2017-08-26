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

    public static function getAllNonVoid($limit = 0, $offset = 0, $search = null, $arrUnitId = null, $arrDivisiId = null)
    {
      if(!empty($limit)) {
        // echo "string - " . $offset; exit();
        if(!empty($search)) {
          if(!empty($arrUnitId)) {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->whereIn('uni_id', $arrUnitId)->limit($limit)->offset($offset)->get();
          } elseif(!empty($arrDivisiId)) {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->whereIn('bag_id', $arrDivisiId)->limit($limit)->offset($offset)->get();
          } else {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->limit($limit)->offset($offset)->get();
          }
        } else {
          if(!empty($arrUnitId)) {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('uni_id', $arrUnitId)->limit($limit)->offset($offset)->get();
          } elseif(!empty($arrDivisiId)) {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('bag_id', $arrDivisiId)->limit($limit)->offset($offset)->get();
          } else {
            $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->limit($limit)->offset($offset)->get();
          }
        }
      } elseif(!empty($search)) {
        if(!empty($arrUnitId)) {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->whereIn('uni_id', $arrUnitId)->get();
        } elseif(!empty($arrDivisiId)) {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->whereIn('bag_id', $arrDivisiId)->get();
        } else {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->where('emp_name', 'LIKE', '%' . $search['value'] . '%')->get();
        }
      } else {
        if(!empty($arrUnitId)) {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('uni_id', $arrUnitId)->get();
        } elseif(!empty($arrDivisiId)) {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('bag_id', $arrDivisiId)->get();
        } else {
          $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->get();
        }
      }
      return $res;
    }

    public static function getEmployeeByID($id)
    {
      $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_id', $id)->first();
      return $res;
    }

    public static function getOptNonVoid($arrUnitId = null, $arrDivisiId = null)
    {
      $arrData = array();

      if(!empty($arrUnitId)) {
        $result = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('uni_id', $arrUnitId)->get();
      } elseif(!empty($arrDivisiId)) {
        $result = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->whereIn('bag_id', $arrDivisiId)->get();
      } else {
        $result = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_void', 0)->get();
      }

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

    public static function getEmployeeByBagian($id = '')
    {
      if(is_array($id)) {
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->whereIn('bag_id', $id)->get();
      } else {
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('bag_id', $id)->get();
      }

      return $res;
    }

    public static function getEmployeeByUnit($id = '')
    {
      if(is_array($id)) {
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->whereIn('uni_id', $id)->get();
      } else {
        $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('uni_id', $id)->get();
      }

      return $res;
    }

    public static function getAllUnit($empId = '')
    {
      $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_id', $empId)->get();
      return $res;
    }

    public static function getAllDivisi($empId = '')
    {
      $res = EmployeeModel::leftjoin('unit', 'emp_uni_id', '=', 'uni_id')->leftjoin('bagian', 'uni_bag_id', '=', 'bag_id')->leftjoin('jabatan', 'emp_jab_id', '=', 'jab_id')->where('emp_id', $empId)->get();
      return $res;
    }
}
