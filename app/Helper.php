<?php

namespace App;

class Helper
{
    public static function formatDBDate($date = '00/00/0000')
    {
        list($month, $day, $year) = explode('/', $date);
        $ret = $year . '-' . $month . '-' . $day;
        return $ret;
    }

    public static function formatDate($date = '0000-00-00')
    {
        list($year, $month, $day) = explode('-', $date);
        $ret = $month . '/' . $day . '/' . $year;
        return $ret;
    }

    public static function dateNowDB()
    {
        return date('Y-m-d H:i:s');
    }
}
