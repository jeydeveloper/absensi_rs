<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayModel extends Model
{
    protected $table      = 'holiday';   // it's always better to specify it
    protected $primaryKey = 'hol_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return HolidayModel::all();
    }

    public static function getAllNonVoid()
    {
      $res = HolidayModel::where('hol_void', 0)->orderBy('hol_tanggal', 'ASC')->get();
      return $res;
    }

    public static function getHolidayByYear($val = '')
    {
      $res = HolidayModel::where('hol_void', 0)
      ->whereRaw('DATE_FORMAT(hol_tanggal, "%Y") = "'.$val.'"')
      ->orderBy('hol_tanggal', 'ASC')
      ->get();
      return $res;
    }
}
