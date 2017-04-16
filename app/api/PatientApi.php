<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\PatientModel as Patient;
use App\Helper;

class PatientApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $data = array(
          'total' => 0,
          'rows' => array()
        );

        $patients = Patient::getAllNonVoid();
        if(!empty($patients)) {
          foreach ($patients as $key => $value) {
            $data['rows'][] = array(
              'ptn_id' => $value->ptn_id,
              'ptn_code' => $value->ptn_code,
              'ptn_fullname' => $value->ptn_fullname,
              'ptn_address' => $value->ptn_address,
              'ptn_birthdate' => $value->ptn_birthdate,
              'ptn_phone' => $value->ptn_phone,
              'ptn_gender' => $value->ptn_gender,
            );
          }
          $data['total'] = count($patients);
        }

        return $response->withJson($data);
    }

    public function doAdd($request, $response, $args)
    {
      $data = array(
          'errMsg' => '',
          'success' => false,
      );

      $ptn_code = $request->getParam('ptn_code');
      $ptn_fullname = $request->getParam('ptn_fullname');
      $ptn_address = $request->getParam('ptn_address');
      $ptn_birthdate = $request->getParam('ptn_birthdate');
      $ptn_phone = $request->getParam('ptn_phone');
      $ptn_gender = $request->getParam('ptn_gender');

      $existCode = Patient::getPatientByCode($ptn_code);
      if($existCode) {
        $data['errMsg'] = 'Oops.. Kode ini sudah digunakan!';
        return $response->withJson($data);
      }

      $patient = new Patient;
      $patient->ptn_code = $ptn_code;
      $patient->ptn_fullname = $ptn_fullname;
      $patient->ptn_address = $ptn_address;
      $patient->ptn_birthdate = Helper::formatDBDate($ptn_birthdate);
      $patient->ptn_phone = $ptn_phone;
      $patient->ptn_gender = $ptn_gender;

      if($patient->save()) {
        $data['success'] = true;
      } else {
        $data['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($data);
    }

    public function doEdit($request, $response, $args)
    {
      $data = array(
          'errMsg' => '',
          'success' => false,
      );

      $ptn_id = $request->getParam('ptn_id');
      $ptn_code = $request->getParam('ptn_code');
      $ptn_fullname = $request->getParam('ptn_fullname');
      $ptn_address = $request->getParam('ptn_address');
      $ptn_birthdate = $request->getParam('ptn_birthdate');
      $ptn_phone = $request->getParam('ptn_phone');
      $ptn_gender = $request->getParam('ptn_gender');

      $existCode = Patient::getPatientByCode($ptn_code);
      if($existCode AND $existCode != $ptn_id) {
        $data['errMsg'] = 'Oops.. Kode ini sudah digunakan!';
        return $response->withJson($data);
      }

      $patient = Patient::find($ptn_id);
      $patient->ptn_code = $ptn_code;
      $patient->ptn_fullname = $ptn_fullname;
      $patient->ptn_address = $ptn_address;
      $patient->ptn_birthdate = Helper::formatDBDate($ptn_birthdate);
      $patient->ptn_phone = $ptn_phone;
      $patient->ptn_gender = $ptn_gender;

      if($patient->save()) {
        $data['success'] = true;
      } else {
        $data['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($data);
    }

    public function edit($request, $response, $args)
    {
      $data = array();

      $ptn_id = $request->getParam('ptn_id');
      $patient = Patient::find($ptn_id);
      if(!empty($patient)) {
        $data['ptn_id'] = $patient->ptn_id;
        $data['ptn_code'] = $patient->ptn_code;
        $data['ptn_fullname'] = $patient->ptn_fullname;
        $data['ptn_gender'] = $patient->ptn_gender;
        $data['ptn_birthdate'] = Helper::formatDate($patient->ptn_birthdate);
        $data['ptn_address'] = $patient->ptn_address;
        $data['ptn_phone'] = $patient->ptn_phone;
      }

      return $response->withJson($data);
    }

    public function doDelete($request, $response, $args)
    {
      $data = array(
          'errMsg' => '',
          'success' => false,
      );

      $ptn_id = $request->getParam('ptn_id');
      $patient = Patient::find($ptn_id);
      $patient->ptn_void = 1;

      if($patient->save()) {
        $data['success'] = true;
      } else {
        $data['errMsg'] = 'Oops.. please try again!';
      }

      return $response->withJson($data);
    }
}
