<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\JabatanModel as Jabatan;
use App\Helper;

class JabatanApi
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

        $result = Jabatan::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->jab_id,
              $value->jab_name,
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

      $jab_name = $request->getParam('jab_name');

      $obj = new Jabatan;
      $obj->jab_name = $jab_name;
      $obj->jab_created_at = Helper::dateNowDB();

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

      $jab_id = $request->getParam('jab_id');
      $jab_name = $request->getParam('jab_name');

      $obj = Jabatan::find($jab_id);
      $obj->jab_name = $jab_name;
      $obj->jab_updated_at = Helper::dateNowDB();

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

      $jab_id = $request->getParam('jab_id');
      $obj = Jabatan::find($jab_id);
      if(!empty($obj)) {
        $arrData['jab_id'] = $obj->jab_id;
        $arrData['jab_name'] = $obj->jab_name;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $jab_id = $request->getParam('jab_id');
      $obj = Jabatan::find($jab_id);
      $obj->jab_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
