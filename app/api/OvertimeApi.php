<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\OvertimeModel as Overtime;
use App\Helper;

class OvertimeApi
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

        $result = Overtime::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->emov_id,
              $value->emov_name,
              $value->emov_tanggal,
              $value->emov_keterangan,
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

      $emov_name = $request->getParam('emov_name');
      $emov_tanggal = $request->getParam('emov_tanggal');
      $emov_keterangan = $request->getParam('emov_keterangan');

      if(!empty($emov_tanggal)) $emov_tanggal = Helper::formatDBDate($emov_tanggal);

      $obj = new Overtime;
      $obj->emov_name = $emov_name;
      $obj->emov_tanggal = $emov_tanggal;
      $obj->emov_keterangan = $emov_keterangan;
      $obj->emov_created_at = Helper::dateNowDB();

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

      $emov_id = $request->getParam('emov_id');
      $emov_name = $request->getParam('emov_name');
      $emov_tanggal = $request->getParam('emov_tanggal');
      $emov_keterangan = $request->getParam('emov_keterangan');

      if(!empty($emov_tanggal)) $emov_tanggal = Helper::formatDBDate($emov_tanggal);

      $obj = Overtime::find($emov_id);
      $obj->emov_name = $emov_name;
      $obj->emov_tanggal = $emov_tanggal;
      $obj->emov_keterangan = $emov_keterangan;
      $obj->emov_updated_at = Helper::dateNowDB();

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

      $emov_id = $request->getParam('emov_id');
      $obj = Overtime::find($emov_id);
      if(!empty($obj)) {
        if(isset($obj->emov_tanggal)) $obj->emov_tanggal = Helper::formatDate($obj->emov_tanggal);

        $arrData['emov_id'] = $obj->emov_id;
        $arrData['emov_name'] = $obj->emov_name;
        $arrData['emov_tanggal'] = $obj->emov_tanggal;
        $arrData['emov_keterangan'] = $obj->emov_keterangan;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $emov_id = $request->getParam('emov_id');
      $obj = Overtime::find($emov_id);
      $obj->emov_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
