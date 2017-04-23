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
      $patient = BagianModel::where('bag_void', 0)->get();
      return $patient;
    }

    public static function getPatientByID($id)
    {
      $patient = BagianModel::where('bag_id', $id)->first();
      return $patient;
    }
}
