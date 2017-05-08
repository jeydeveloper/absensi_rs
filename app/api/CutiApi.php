<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\CutiModel as Cuti;
use App\Helper;

class CutiApi
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

        $result = Cuti::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->cut_id,
              $value->cut_name,
              $value->cut_jumlah,
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

      $cut_name = $request->getParam('cut_name');
      $cut_jumlah = $request->getParam('cut_jumlah');

      if(!empty($cut_tanggal)) $cut_tanggal = Helper::formatDBDate($cut_tanggal);

      $obj = new Cuti;
      $obj->cut_name = $cut_name;
      $obj->cut_jumlah = $cut_jumlah;
      $obj->cut_created_at = Helper::dateNowDB();

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

      $cut_id = $request->getParam('cut_id');
      $cut_name = $request->getParam('cut_name');
      $cut_jumlah = $request->getParam('cut_jumlah');

      if(!empty($cut_tanggal)) $cut_tanggal = Helper::formatDBDate($cut_tanggal);

      $obj = Cuti::find($cut_id);
      $obj->cut_name = $cut_name;
      $obj->cut_jumlah = $cut_jumlah;
      $obj->cut_updated_at = Helper::dateNowDB();

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

      $cut_id = $request->getParam('cut_id');
      $obj = Cuti::find($cut_id);
      if(!empty($obj)) {
        if(isset($obj->cut_tanggal)) $obj->cut_tanggal = Helper::formatDate($obj->cut_tanggal);

        $arrData['cut_id'] = $obj->cut_id;
        $arrData['cut_name'] = $obj->cut_name;
        $arrData['cut_jumlah'] = $obj->cut_jumlah;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $cut_id = $request->getParam('cut_id');
      $obj = Cuti::find($cut_id);
      $obj->cut_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
