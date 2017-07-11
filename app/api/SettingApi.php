<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\SettingModel as Setting;
use App\Helper;

class SettingApi
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

        $result = Setting::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->sett_id,
              $value->sett_name,
              $value->sett_value,
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

      $sett_name = $request->getParam('sett_name');
      $sett_value = $request->getParam('sett_value');

      $obj = new Setting;
      $obj->sett_name = $sett_name;
      $obj->sett_value = $sett_value;
      $obj->sett_created_at = Helper::dateNowDB();

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

      $sett_id = $request->getParam('sett_id');
      //$sett_name = $request->getParam('sett_name');
      $sett_value = $request->getParam('sett_value');

      $obj = Setting::find($sett_id);
      //$obj->sett_name = $sett_name;
      $obj->sett_value = $sett_value;
      $obj->sett_updated_at = Helper::dateNowDB();

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

      $sett_id = $request->getParam('sett_id');
      $obj = Setting::find($sett_id);
      if(!empty($obj)) {
        $arrData['sett_id'] = $obj->sett_id;
        $arrData['sett_name'] = $obj->sett_name;
        $arrData['sett_value'] = $obj->sett_value;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $sett_id = $request->getParam('sett_id');
      $obj = Setting::find($sett_id);
      $obj->sett_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
