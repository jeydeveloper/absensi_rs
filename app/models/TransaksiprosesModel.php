<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiprosesModel extends Model
{
    protected $table      = 'transaksi_proses';   // it's always better to specify it
    protected $primaryKey = 'trpr_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return TransaksiprosesModel::all();
    }

    public static function getByID($id)
    {
      $res = TransaksiprosesModel::where('trpr_id', $id)->first();
      return $res;
    }

    public static function getByUniqCode($uniqCode)
    {
      $res = TransaksiprosesModel::where('trpr_uniq_code', $uniqCode)->first();
      return $res;
    }

    public static function doMultipleInsert($data = null) {
        if(empty($data) OR !is_array($data)) return 0;
        $res = TransaksiprosesModel::insert($data);
        return $res;
    }
}
