<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Helper;

class EmployeeApi
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

        $result = Employee::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->emp_id,
              $value->emp_name,
              $value->emp_uni_id,
              $value->emp_jab_id,
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

      $emp_name = $request->getParam('emp_name');
      $emp_uni_id = $request->getParam('emp_uni_id');
      $emp_jab_id = $request->getParam('emp_jab_id');

      $obj = new Employee;
      $obj->emp_name = $emp_name;
      $obj->emp_uni_id = $emp_uni_id;
      $obj->emp_jab_id = $emp_jab_id;
      $obj->emp_created_at = Helper::dateNowDB();

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

      $emp_id = $request->getParam('emp_id');
      $emp_name = $request->getParam('emp_name');
      $emp_uni_id = $request->getParam('emp_uni_id');
      $emp_jab_id = $request->getParam('emp_jab_id');

      $obj = Employee::find($emp_id);
      $obj->emp_name = $emp_name;
      $obj->emp_uni_id = $emp_uni_id;
      $obj->emp_jab_id = $emp_jab_id;
      $obj->emp_updated_at = Helper::dateNowDB();

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

      $emp_id = $request->getParam('emp_id');
      $obj = Employee::find($emp_id);
      if(!empty($obj)) {
        $arrData['emp_id'] = $obj->emp_id;
        $arrData['emp_name'] = $obj->emp_name;
        $arrData['emp_uni_id'] = $obj->emp_uni_id;
        $arrData['emp_jab_id'] = $obj->emp_jab_id;
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $emp_id = $request->getParam('emp_id');
      $obj = Employee::find($emp_id);
      $obj->emp_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
