<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\UnitModel as Unit;
use App\Helper;

class UnitApi
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

        $result = Unit::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->uni_id,
              $value->uni_name,
              $value->bag_name,
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

      $uni_name = $request->getParam('uni_name');
      $uni_bag_id = $request->getParam('uni_bag_id');

      $obj = new Unit;
      $obj->uni_name = $uni_name;
      $obj->uni_bag_id = $uni_bag_id;
      $obj->uni_created_at = Helper::dateNowDB();

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

      $uni_id = $request->getParam('uni_id');
      $uni_name = $request->getParam('uni_name');
      $uni_bag_id = $request->getParam('uni_bag_id');

      $obj = Unit::find($uni_id);
      $obj->uni_name = $uni_name;
      $obj->uni_bag_id = $uni_bag_id;
      $obj->uni_updated_at = Helper::dateNowDB();

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

      $uni_id = $request->getParam('uni_id');
      $obj = Unit::find($uni_id);
      if(!empty($obj)) {
        $arrData['uni_id'] = $obj->uni_id;
        $arrData['uni_name'] = $obj->uni_name;
        $arrData['uni_bag_id'] = $obj->uni_bag_id;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $uni_id = $request->getParam('uni_id');
      $obj = Unit::find($uni_id);
      $obj->uni_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
