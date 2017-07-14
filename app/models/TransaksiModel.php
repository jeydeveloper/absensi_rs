<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    protected $table      = 'transaksi';   // it's always better to specify it
    protected $primaryKey = 'tran_id';     // must be defined if different from 'id'
    public    $timestamps = false;     // to get rid of created_at and updated_at

    public static function getAll()
    {
        return TransaksiModel::all();
    }

    public static function getAllMaxTranTime($dateStart = '', $dateEnd = '', $cardNo = '')
    {
      /*
      $month = !empty($month) ? $month : date('m');
      $year = !empty($year) ? $year : date('Y');
      $jumlahTanggal = date('t', strtotime("$year-$month-01"));

      $dateStart = "$year-$month-01";
      $dateEnd = "$year-$month-$jumlahTanggal";
      */

      $dateStart = !empty($dateStart) ? $dateStart : date('Y-m-01');
      $dateEnd = !empty($dateEnd) ? $dateEnd : date('Y-m-t');

      $res = TransaksiModel::selectRaw("tran_cardNo, MAX(tran_time) as wkt, DATE_FORMAT(tran_time, '%Y-%m-%d') as tgl")
      ->orderBy('tran_cardNo', 'DESC')
      ->groupBy('tran_cardNo')
      ->groupBy('tgl')
      ->havingRaw('tgl BETWEEN "'.$dateStart.'" AND "'.$dateEnd.'"')
      ->get();
      return $res;
    }

    public static function getAllMinTranTime($dateStart = '', $dateEnd = '', $cardNo = '')
    {
      /*
      $month = !empty($month) ? $month : date('m');
      $year = !empty($year) ? $year : date('Y');
      $jumlahTanggal = date('t', strtotime("$year-$month-01"));

      $dateStart = "$year-$month-01";
      $dateEnd = "$year-$month-$jumlahTanggal";
      */

      $dateStart = !empty($dateStart) ? $dateStart : date('Y-m-01');
      $dateEnd = !empty($dateEnd) ? $dateEnd : date('Y-m-t');

      $res = TransaksiModel::selectRaw("tran_cardNo, MIN(tran_time) as wkt, DATE_FORMAT(tran_time, '%Y-%m-%d') as tgl")
      ->orderBy('tran_cardNo', 'DESC')
      ->groupBy('tran_cardNo')
      ->groupBy('tgl')
      ->havingRaw('tgl BETWEEN "'.$dateStart.'" AND "'.$dateEnd.'"')
      ->get();
      return $res;
    }

    public static function getAllMinMaxTranTime($dateStart = '', $dateEnd = '', $data = null)
    {
      if(!is_array($data)) return 0;

      $dateStart = !empty($dateStart) ? $dateStart : date('Y-m-01');
      $dateEnd = !empty($dateEnd) ? $dateEnd : date('Y-m-t');

      list($year, $month, $day) = explode('-', $dateEnd);
      $lastDate = date('t', strtotime($dateEnd));
      if($day == $lastDate) {
        $newMonth = 1 + (int)$month;
        if($newMonth > 12) {
          $newMonth = 1;
          $year += 1;
        }
        $newMonth = $newMonth < 10 ? "0$newMonth" : $newMonth;
        $dateEnd = $year . '-' . $newMonth . '-01';
      } else {
        $newTanggal = 1 + (int)$day;
        $newTanggal = $newTanggal < 10 ? "0$newTanggal" : $newTanggal;
        $dateEnd = $year . '-' . $month . '-' . $newTanggal;
      }

      $res = TransaksiModel::selectRaw("tran_cardNo, MIN(tran_time) as wkt_min, MAX(tran_time) as wkt_max, time(MIN(tran_time)) as time_min, time(MAX(tran_time)) as time_max, date(tran_time) as tgl")
      ->whereIn('tran_cardNo', $data)
      ->whereRaw('date(tran_time) BETWEEN "'.$dateStart.'" AND "'.$dateEnd.'"')
      ->groupBy('tran_cardNo')
      ->groupBy('tgl')
      ->orderBy('tgl', 'ASC')
      ->get();
      return $res;
    }

    public static function getByID($id)
    {
      $res = TransaksiModel::where('tran_id', $id)->first();
      return $res;
    }
}
