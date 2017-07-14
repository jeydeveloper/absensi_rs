<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\EmployeeModel as Employee;
use App\Models\IzinModel as Izin;
use App\Models\SettingModel as Setting;
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

        $limit = !empty($request->getParam('length')) ? $request->getParam('length') : 10;
        $offset = !empty($request->getParam('start')) ? $request->getParam('start') : 0;

        $year = !empty($request->getParam('slYear')) ? $request->getParam('slYear') : date('Y');

        $resultTotal = Employee::getAllNonVoid();
        $result = Employee::getAllNonVoid($limit, $offset);
        if(!empty($result)) {
          $arrData['recordsTotal'] = count($resultTotal);
          $arrData['recordsFiltered'] = count($resultTotal);

          $setting = $this->getSettingDb();
          $jumlahCuti = !empty($setting['jumlah_cuti']) ? $setting['jumlah_cuti'] : 0;

          $dataEmpCuti = [];
          $dataEmpId = [];
          foreach ($result as $key => $value) {
            $dataEmpId[$value->emp_id] = $value->emp_id;
          }

          if(!empty($dataEmpId)) {
            $res = Izin::getUserCuti($dataEmpId, $year);
            if(!empty($res)) {
              foreach ($res as $key => $value) {
                $value->cuti += 1;
                $dataEmpCuti[$value->emcu_emp_id] = [
                  'totalCuti' => $value->cuti,
                  'sisaCuti' => ($jumlahCuti - $value->cuti),
                ];
              }
            }
          }

          //print_r($dataEmpCuti);

          foreach ($result as $key => $value) {
            $arrData['data'][$key] = array(
              ($key + 1),
              $value->emp_id,
              $value->emp_code,
              ('<a href="'.($this->ci->get('settings')['baseUrl'] . 'cuti/report?empId='.$value->emp_id.'&year='.$year).'" class="btn-link">'.$value->emp_name.'</a>'),
              (!empty($dataEmpCuti[$value->emp_id]) ? $dataEmpCuti[$value->emp_id]['sisaCuti'] : $jumlahCuti)
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

    public function getSettingDb() {
      $arrData = [];
      $setting = Setting::getAllNonVoid();
      foreach ($setting as $key => $value) {
        $arrData[$value->sett_name] = $value->sett_value;
      }
      return $arrData;
    }
}
