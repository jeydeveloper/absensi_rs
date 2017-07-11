<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\HolidayModel as Holiday;
use App\Helper;

class HolidayApi
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

        $year = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : '';

        if(!empty($year) AND $year != 'all') {
          $result = Holiday::getHolidayByYear($year);
        } else {
          $result = Holiday::getAllNonVoid();
        }

        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->hol_id,
              $value->hol_name,
              $value->hol_tanggal,
              $value->hol_keterangan,
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

      $hol_name = $request->getParam('hol_name');
      $hol_tanggal = $request->getParam('hol_tanggal');
      $hol_keterangan = $request->getParam('hol_keterangan');

      if(!empty($hol_tanggal)) $hol_tanggal = Helper::formatDBDate($hol_tanggal);

      $obj = new Holiday;
      $obj->hol_name = $hol_name;
      $obj->hol_tanggal = $hol_tanggal;
      $obj->hol_keterangan = $hol_keterangan;
      $obj->hol_created_at = Helper::dateNowDB();

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

      $hol_id = $request->getParam('hol_id');
      $hol_name = $request->getParam('hol_name');
      $hol_tanggal = $request->getParam('hol_tanggal');
      $hol_keterangan = $request->getParam('hol_keterangan');

      if(!empty($hol_tanggal)) $hol_tanggal = Helper::formatDBDate($hol_tanggal);

      $obj = Holiday::find($hol_id);
      $obj->hol_name = $hol_name;
      $obj->hol_tanggal = $hol_tanggal;
      $obj->hol_keterangan = $hol_keterangan;
      $obj->hol_updated_at = Helper::dateNowDB();

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

      $hol_id = $request->getParam('hol_id');
      $obj = Holiday::find($hol_id);
      if(!empty($obj)) {
        if(isset($obj->hol_tanggal)) $obj->hol_tanggal = Helper::formatDate($obj->hol_tanggal);

        $arrData['hol_id'] = $obj->hol_id;
        $arrData['hol_name'] = $obj->hol_name;
        $arrData['hol_tanggal'] = $obj->hol_tanggal;
        $arrData['hol_keterangan'] = $obj->hol_keterangan;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $hol_id = $request->getParam('hol_id');
      $obj = Holiday::find($hol_id);
      $obj->hol_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
