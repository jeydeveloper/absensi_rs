<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleModel extends Model
{
    protected $table      = 'schedule';   // it's always better to specify it
    protected $primaryKey = 'schd_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return ScheduleModel::all();
    }

    public static function getAllNonVoid()
    {
      $patient = ScheduleModel::where('schd_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = ScheduleModel::where('schd_id', $id)->first();
      return $patient;
    }

    public static function getOptNonVoid()
    {
        $arrData = array();

        $result = ScheduleModel::where('schd_void', 0)->get();

        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $arrData[$value->schd_code] = array(
                    'schd_code' => $value->schd_code,
                    'schd_color' => $value->schd_color,
                );
            }
        }

        return $arrData;
    }

    public static function getForReportNonVoid()
    {
        $arrData = array();

        $result = ScheduleModel::where('schd_void', 0)->get();

        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $arrData[] = array(
                    'schd_id' => $value->schd_id,
                    'schd_name' => $value->schd_name,
                );
            }
        }

        return $arrData;
    }
}
