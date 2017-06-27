<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\JadwalkerjaModel as Jadwalkerja;
use App\Helper;

class JadwalkerjaApi
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

        $randColor = [
          'btn-info',
          'btn-success',
          'btn-warning',
          'btn-primary2',
          'btn-danger',
          'btn-default',
        ];

        $randShift = [
          'SFT-01',
          'SFT-02',
          'SFT-03',
          'SFT-04',
          'OFF',
          'TM',
        ];

        $result = Jadwalkerja::getAllNonVoid();
        if(!empty($result)) {
          $cnt = 0;
          foreach ($result as $key => $value) {
            $arrData['data'][$key] = array(
              ($key + 1),
              $value->sta_id,
              1234,
              'User ' . ($key + 1),
            );
            $len = count($arrData['data'][$key]);
            $forLimit = 31 + $len;

            for ($i=$len; $i <= $forLimit; $i++) {
              $rand = rand(0,5);
              $lblShift = $randShift[$rand];
              $lblButtonStatusToUpdate = "btnStatusToUpdate_$cnt";
              $arrData['data'][$key][$i] = '<button id="'.$lblButtonStatusToUpdate.'" type="button" class="btn btn-block btn-outline '.$randColor[$rand].' btn-sm" onclick="doAlert(\''.$lblButtonStatusToUpdate.'\', \''.$lblShift.'\')">'.$lblShift.'</button>';
              $cnt++;
            }
          }
        }

        return $response->withJson($arrData);
    }

    public function doEdit($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $randColor = [
        'btn-info',
        'btn-success',
        'btn-warning',
        'btn-primary2',
        'btn-danger',
        'btn-default',
      ];

      $randShift = [
        'SFT-01',
        'SFT-02',
        'SFT-03',
        'SFT-04',
        'OFF',
        'TM',
      ];

      $hdStatus = $request->getParam('hdStatus');
      $txtAlasan = $request->getParam('txtAlasan');

      $key = array_search($hdStatus, $randShift);

      if(isset($key)) {
        $lblShift = $randShift[$key];
        $arrData['button'] = '<button type="button" class="btn btn-block btn-outline '.$randColor[$key].' btn-sm" onclick="doAlert('.($key + 1).', \''.$lblShift.'\')">'.$lblShift.'</button>';

        $arrData['success'] = true;
        $arrData['message'] = 'Update data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
