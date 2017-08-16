<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UseradminModel extends Model
{
    protected $table      = 'users';   // it's always better to specify it
    protected $primaryKey = 'usr_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return UseradminModel::all();
    }

    public static function getAllNonVoid()
    {
      $result = UseradminModel::leftjoin('role_access', 'usr_role_id', '=', 'role_id')->leftjoin('employee', 'usr_emp_id', '=', 'emp_id')->where('usr_void', 0)->where('usr_id', '!=', 1)->get();
      return $result;
    }

    public static function getUserByID($id)
    {
      $result = UseradminModel::leftjoin('role_access', 'usr_role_id', '=', 'role_id')->leftjoin('employee', 'usr_emp_id', '=', 'emp_id')->where('usr_void', 0)->where('usr_id', '!=', 1)->where('usr_id', $id)->first();
      return $result;
    }
}
