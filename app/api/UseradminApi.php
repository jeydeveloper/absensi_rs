<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\UseradminModel as Useradmin;
use App\Helper;

class UseradminApi
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

        $result = Useradmin::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->usr_id,
              $value->usr_username,
              $value->role_name,
              $value->emp_name,
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

      $usr_username = $request->getParam('usr_username');
      $usr_password = $request->getParam('usr_password');
      $usr_role_id = $request->getParam('usr_role_id');
      $usr_emp_id = $request->getParam('usr_emp_id');

      $obj = new Useradmin;
      $obj->usr_username = $usr_username;
      $obj->usr_password = md5($usr_password);
      $obj->usr_role_id = $usr_role_id;
      $obj->usr_emp_id = $usr_emp_id;
      $obj->usr_created_at = Helper::dateNowDB();

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

      $usr_id = $request->getParam('usr_id');
      $usr_username = $request->getParam('usr_username');
      $usr_password = $request->getParam('usr_password');
      $usr_role_id = $request->getParam('usr_role_id');
      $usr_emp_id = $request->getParam('usr_emp_id');

      $obj = Useradmin::find($usr_id);
      $obj->usr_username = $usr_username;
      if(!empty($usr_password)) $obj->usr_password = md5($usr_password);
      $obj->usr_role_id = $usr_role_id;
      $obj->usr_emp_id = $usr_emp_id;
      $obj->usr_updated_at = Helper::dateNowDB();

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

      $usr_id = $request->getParam('usr_id');
      $obj = Useradmin::find($usr_id);
      if(!empty($obj)) {
        $arrData['usr_id'] = $obj->usr_id;
        $arrData['usr_username'] = $obj->usr_username;
        $arrData['usr_role_id'] = $obj->usr_role_id;
        $arrData['usr_emp_id'] = $obj->usr_emp_id;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $usr_id = $request->getParam('usr_id');
      $obj = Useradmin::find($usr_id);
      $obj->usr_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
