<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\ScheduleModel as Schedule;
use App\Helper;

class ScheduleApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $arrData = array(
          'data' => array()
        );

        $result = Schedule::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $color = '<button type="button" class="btn btn-block btn-sm" style="background-color:'.$value->schd_color.' !important;color:#ffffff;">'.$value->schd_code.'</button>';
            $arrData['data'][] = array(
              ($key + 1),
              $value->schd_id,
              $value->schd_code,
              $value->schd_name,
              $value->schd_waktu_awal,
              $value->schd_waktu_akhir,
              $color,
            );
          }
        }

        return $response->withJson($arrData);
    }

    public function doAdd($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $schd_name = $request->getParam('schd_name');
      $schd_waktu_awal = $request->getParam('schd_waktu_awal');
      $schd_waktu_akhir = $request->getParam('schd_waktu_akhir');
      $schd_keterangan = $request->getParam('schd_keterangan');
      $schd_code = $request->getParam('schd_code');
      $schd_color = $request->getParam('schd_color');
      $schd_ganti_hari = !empty($request->getParam('schd_ganti_hari')) ? $request->getParam('schd_ganti_hari') : 0;

      if(!empty($schd_tanggal)) $schd_tanggal = Helper::formatDBDate($schd_tanggal);

      $obj = new Schedule;
      $obj->schd_name = $schd_name;
      $obj->schd_waktu_awal = $schd_waktu_awal;
      $obj->schd_waktu_akhir = $schd_waktu_akhir;
      $obj->schd_keterangan = $schd_keterangan;
      $obj->schd_created_at = Helper::dateNowDB();
      $obj->schd_code = $schd_code;
      $obj->schd_color = $schd_color;
      $obj->schd_ganti_hari = $schd_ganti_hari;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Insert data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }

    public function doEdit($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $schd_id = $request->getParam('schd_id');
      $schd_name = $request->getParam('schd_name');
      $schd_waktu_awal = $request->getParam('schd_waktu_awal');
      $schd_waktu_akhir = $request->getParam('schd_waktu_akhir');
      $schd_keterangan = $request->getParam('schd_keterangan');
      $schd_code = $request->getParam('schd_code');
      $schd_color = $request->getParam('schd_color');
      $schd_ganti_hari = !empty($request->getParam('schd_ganti_hari')) ? $request->getParam('schd_ganti_hari') : 0;

      if(!empty($schd_tanggal)) $schd_tanggal = Helper::formatDBDate($schd_tanggal);

      $obj = Schedule::find($schd_id);
      $obj->schd_name = $schd_name;
      $obj->schd_waktu_awal = $schd_waktu_awal;
      $obj->schd_waktu_akhir = $schd_waktu_akhir;
      $obj->schd_keterangan = $schd_keterangan;
      $obj->schd_updated_at = Helper::dateNowDB();
      $obj->schd_code = $schd_code;
      $obj->schd_color = $schd_color;
      $obj->schd_ganti_hari = $schd_ganti_hari;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Update data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }

    public function edit($request, $response, $args)
    {
      $arrData = array();

      $schd_id = $request->getParam('schd_id');
      $obj = Schedule::find($schd_id);
      if(!empty($obj)) {
        if(isset($obj->schd_tanggal)) $obj->schd_tanggal = Helper::formatDate($obj->schd_tanggal);

        $arrData['schd_id'] = $obj->schd_id;
        $arrData['schd_name'] = $obj->schd_name;
        $arrData['schd_waktu_awal'] = $obj->schd_waktu_awal;
        $arrData['schd_waktu_akhir'] = $obj->schd_waktu_akhir;
        $arrData['schd_keterangan'] = $obj->schd_keterangan;
        $arrData['schd_code'] = $obj->schd_code;
        $arrData['schd_color'] = $obj->schd_color;
        $arrData['schd_ganti_hari'] = $obj->schd_ganti_hari;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $schd_id = $request->getParam('schd_id');
      $obj = Schedule::find($schd_id);
      $obj->schd_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
