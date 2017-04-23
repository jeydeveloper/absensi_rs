<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\BagianModel as Bagian;
use App\Helper;

class BagianApi
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

        $result = Bagian::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->bag_id,
              $value->bag_name,
            );
          }
        }

        return $response->withJson($arrData);
    }

    public function doAdd($request, $response, $args)
    {
      $arrData = array(
          'errMsg' => '',
          'success' => false,
      );

      $bag_name = $request->getParam('bag_name');

      $obj = new Bagian;
      $obj->bag_name = $bag_name;
      $obj->bag_created_at = Helper::dateNowDB();

      if($obj->save()) {
        $arrData['success'] = true;
      } else {
        $arrData['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }

    public function doEdit($request, $response, $args)
    {
      $arrData = array(
          'errMsg' => '',
          'success' => false,
      );

      $bag_id = $request->getParam('bag_id');
      $bag_name = $request->getParam('bag_name');

      $obj = Bagian::find($bag_id);
      $obj->bag_name = $bag_name;
      $obj->bag_updated_at = Helper::dateNowDB();

      if($obj->save()) {
        $arrData['success'] = true;
      } else {
        $arrData['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }

    public function edit($request, $response, $args)
    {
      $arrData = array();

      $bag_id = $request->getParam('bag_id');
      $obj = Bagian::find($bag_id);
      if(!empty($obj)) {
        $arrData['bag_id'] = $obj->bag_id;
        $arrData['bag_name'] = $obj->bag_name;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'errMsg' => '',
          'success' => false,
      );

      $bag_id = $request->getParam('bag_id');
      $obj = Bagian::find($bag_id);
      $obj->bag_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
      } else {
        $arrData['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
