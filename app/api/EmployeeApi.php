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
              $value->emp_code,
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
      $emp_initial = $request->getParam('emp_initial');
      $emp_code = $request->getParam('emp_code');
      $emp_NIK = $request->getParam('emp_NIK');
      $emp_gender = $request->getParam('emp_gender');
      $emp_birthplace = $request->getParam('emp_birthplace');
      $emp_birthdate = $request->getParam('emp_birthdate');
      $emp_religion = $request->getParam('emp_religion');
      $emp_marital_status = $request->getParam('emp_marital_status');
      $emp_address1 = $request->getParam('emp_address1');
      $emp_address2 = $request->getParam('emp_address2');
      $emp_address3 = $request->getParam('emp_address3');
      $emp_post_code = $request->getParam('emp_post_code');
      $emp_ext_phone = $request->getParam('emp_ext_phone');
      $emp_office_phone = $request->getParam('emp_office_phone');
      $emp_home_phone = $request->getParam('emp_home_phone');
      $emp_mobile_phone = $request->getParam('emp_mobile_phone');
      $emp_pin_bb = $request->getParam('emp_pin_bb');
      $emp_email = $request->getParam('emp_email');
      $emp_website = $request->getParam('emp_website');
      $emp_acc_bank = $request->getParam('emp_acc_bank');
      $emp_acc_no = $request->getParam('emp_acc_no');
      $emp_acc_name = $request->getParam('emp_acc_name');
      $emp_insurance = $request->getParam('emp_insurance');
      $emp_insurance_no = $request->getParam('emp_insurance_no');
      $emp_active = $request->getParam('emp_active');
      $emp_start_date = $request->getParam('emp_start_date');
      $emp_out_date = $request->getParam('emp_out_date');
      $emp_reason_out = $request->getParam('emp_reason_out');
      $emp_photo = $request->getParam('emp_photo');
      $emp_base_salary = $request->getParam('emp_base_salary');
      $emp_uni_id = $request->getParam('emp_uni_id');
      $emp_jab_id = $request->getParam('emp_jab_id');

      if(!empty($emp_birthdate)) $emp_birthdate = Helper::formatDBDate($emp_birthdate);
      if(!empty($emp_start_date)) $emp_start_date = Helper::formatDBDate($emp_start_date);
      if(!empty($emp_out_date)) $emp_out_date = Helper::formatDBDate($emp_out_date);
      if(!empty($emp_base_salary)) $emp_base_salary = Helper::formatDBCurrency($emp_base_salary);

      $obj = new Employee;
      $obj->emp_name = $emp_name;
      $obj->emp_initial = $emp_initial;
      $obj->emp_code = $emp_code;
      $obj->emp_NIK = $emp_NIK;
      $obj->emp_gender = $emp_gender;
      $obj->emp_birthplace = $emp_birthplace;
      $obj->emp_birthdate = $emp_birthdate;
      $obj->emp_religion = $emp_religion;
      $obj->emp_marital_status = $emp_marital_status;
      $obj->emp_address1 = $emp_address1;
      $obj->emp_address2 = $emp_address2;
      $obj->emp_address3 = $emp_address3;
      $obj->emp_post_code = $emp_post_code;
      $obj->emp_ext_phone = $emp_ext_phone;
      $obj->emp_office_phone = $emp_office_phone;
      $obj->emp_home_phone = $emp_home_phone;
      $obj->emp_mobile_phone = $emp_mobile_phone;
      $obj->emp_pin_bb = $emp_pin_bb;
      $obj->emp_email = $emp_email;
      $obj->emp_website = $emp_website;
      $obj->emp_acc_bank = $emp_acc_bank;
      $obj->emp_acc_no = $emp_acc_no;
      $obj->emp_acc_name = $emp_acc_name;
      $obj->emp_insurance = $emp_insurance;
      $obj->emp_insurance_no = $emp_insurance_no;
      $obj->emp_active = $emp_active;
      $obj->emp_start_date = $emp_start_date;
      $obj->emp_out_date = $emp_out_date;
      $obj->emp_reason_out = $emp_reason_out;
      $obj->emp_photo = $emp_photo;
      $obj->emp_base_salary = $emp_base_salary;
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
      $emp_initial = $request->getParam('emp_initial');
      $emp_code = $request->getParam('emp_code');
      $emp_NIK = $request->getParam('emp_NIK');
      $emp_gender = $request->getParam('emp_gender');
      $emp_birthplace = $request->getParam('emp_birthplace');
      $emp_birthdate = $request->getParam('emp_birthdate');
      $emp_religion = $request->getParam('emp_religion');
      $emp_marital_status = $request->getParam('emp_marital_status');
      $emp_address1 = $request->getParam('emp_address1');
      $emp_address2 = $request->getParam('emp_address2');
      $emp_address3 = $request->getParam('emp_address3');
      $emp_post_code = $request->getParam('emp_post_code');
      $emp_ext_phone = $request->getParam('emp_ext_phone');
      $emp_office_phone = $request->getParam('emp_office_phone');
      $emp_home_phone = $request->getParam('emp_home_phone');
      $emp_mobile_phone = $request->getParam('emp_mobile_phone');
      $emp_pin_bb = $request->getParam('emp_pin_bb');
      $emp_email = $request->getParam('emp_email');
      $emp_website = $request->getParam('emp_website');
      $emp_acc_bank = $request->getParam('emp_acc_bank');
      $emp_acc_no = $request->getParam('emp_acc_no');
      $emp_acc_name = $request->getParam('emp_acc_name');
      $emp_insurance = $request->getParam('emp_insurance');
      $emp_insurance_no = $request->getParam('emp_insurance_no');
      $emp_active = $request->getParam('emp_active');
      $emp_start_date = $request->getParam('emp_start_date');
      $emp_out_date = $request->getParam('emp_out_date');
      $emp_reason_out = $request->getParam('emp_reason_out');
      $emp_photo = $request->getParam('emp_photo');
      $emp_base_salary = $request->getParam('emp_base_salary');
      $emp_uni_id = $request->getParam('emp_uni_id');
      $emp_jab_id = $request->getParam('emp_jab_id');

      if(!empty($emp_birthdate)) $emp_birthdate = Helper::formatDBDate($emp_birthdate);
      if(!empty($emp_start_date)) $emp_start_date = Helper::formatDBDate($emp_start_date);
      if(!empty($emp_out_date)) $emp_out_date = Helper::formatDBDate($emp_out_date);
      if(!empty($emp_base_salary)) $emp_base_salary = Helper::formatDBCurrency($emp_base_salary);

      $obj = Employee::find($emp_id);
      $obj->emp_name = $emp_name;
      $obj->emp_initial = $emp_initial;
      $obj->emp_code = $emp_code;
      $obj->emp_NIK = $emp_NIK;
      $obj->emp_gender = $emp_gender;
      $obj->emp_birthplace = $emp_birthplace;
      $obj->emp_birthdate = $emp_birthdate;
      $obj->emp_religion = $emp_religion;
      $obj->emp_marital_status = $emp_marital_status;
      $obj->emp_address1 = $emp_address1;
      $obj->emp_address2 = $emp_address2;
      $obj->emp_address3 = $emp_address3;
      $obj->emp_post_code = $emp_post_code;
      $obj->emp_ext_phone = $emp_ext_phone;
      $obj->emp_office_phone = $emp_office_phone;
      $obj->emp_home_phone = $emp_home_phone;
      $obj->emp_mobile_phone = $emp_mobile_phone;
      $obj->emp_pin_bb = $emp_pin_bb;
      $obj->emp_email = $emp_email;
      $obj->emp_website = $emp_website;
      $obj->emp_acc_bank = $emp_acc_bank;
      $obj->emp_acc_no = $emp_acc_no;
      $obj->emp_acc_name = $emp_acc_name;
      $obj->emp_insurance = $emp_insurance;
      $obj->emp_insurance_no = $emp_insurance_no;
      $obj->emp_active = $emp_active;
      $obj->emp_start_date = $emp_start_date;
      $obj->emp_out_date = $emp_out_date;
      $obj->emp_reason_out = $emp_reason_out;
      $obj->emp_photo = $emp_photo;
      $obj->emp_base_salary = $emp_base_salary;
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
        if(isset($obj->emp_birthdate)) $obj->emp_birthdate = Helper::formatDate($obj->emp_birthdate);
        if(isset($obj->emp_start_date)) $obj->emp_start_date = Helper::formatDate($obj->emp_start_date);
        if(isset($obj->emp_out_date)) $obj->emp_out_date = Helper::formatDate($obj->emp_out_date);
        if(isset($obj->emp_base_salary)) $obj->emp_base_salary = Helper::formatCurrency($obj->emp_base_salary);

        $arrData['emp_id'] = $obj->emp_id;
        $arrData['emp_name'] = $obj->emp_name;
        $arrData['emp_initial'] = $obj->emp_initial;
        $arrData['emp_code'] = $obj->emp_code;
        $arrData['emp_NIK'] = $obj->emp_NIK;
        $arrData['emp_gender'] = $obj->emp_gender;
        $arrData['emp_birthplace'] = $obj->emp_birthplace;
        $arrData['emp_birthdate'] = $obj->emp_birthdate;
        $arrData['emp_religion'] = $obj->emp_religion;
        $arrData['emp_marital_status'] = $obj->emp_marital_status;
        $arrData['emp_address1'] = $obj->emp_address1;
        $arrData['emp_address2'] = $obj->emp_address2;
        $arrData['emp_address3'] = $obj->emp_address3;
        $arrData['emp_post_code'] = $obj->emp_post_code;
        $arrData['emp_ext_phone'] = $obj->emp_ext_phone;
        $arrData['emp_office_phone'] = $obj->emp_office_phone;
        $arrData['emp_home_phone'] = $obj->emp_home_phone;
        $arrData['emp_mobile_phone'] = $obj->emp_mobile_phone;
        $arrData['emp_pin_bb'] = $obj->emp_pin_bb;
        $arrData['emp_email'] = $obj->emp_email;
        $arrData['emp_website'] = $obj->emp_website;
        $arrData['emp_acc_bank'] = $obj->emp_acc_bank;
        $arrData['emp_acc_no'] = $obj->emp_acc_no;
        $arrData['emp_acc_name'] = $obj->emp_acc_name;
        $arrData['emp_insurance'] = $obj->emp_insurance;
        $arrData['emp_insurance_no'] = $obj->emp_insurance_no;
        $arrData['emp_active'] = $obj->emp_active;
        $arrData['emp_start_date'] = $obj->emp_start_date;
        $arrData['emp_out_date'] = $obj->emp_out_date;
        $arrData['emp_reason_out'] = $obj->emp_reason_out;
        $arrData['emp_photo'] = $obj->emp_photo;
        $arrData['emp_base_salary'] = $obj->emp_base_salary;
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
