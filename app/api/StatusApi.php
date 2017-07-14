<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\StatusModel as Status;
use App\Helper;

class StatusApi
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

        $result = Status::getAllNonVoid();
        if(!empty($result)) {
          foreach ($result as $key => $value) {
            $arrData['data'][] = array(
              ($key + 1),
              $value->sta_id,
              $value->sta_name,
              (!empty($value->sta_type) ? ($value->sta_type == 1 ? 'Hadir' : ($value->sta_type == 2 ? 'Tidak Hadir' : 'Terlambat')) : '-'),
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

      $sta_name = $request->getParam('sta_name');
      $sta_type = $request->getParam('sta_type');
      $sta_flag_cuti = !empty($request->getParam('sta_flag_cuti')) ? $request->getParam('sta_flag_cuti') : 0;

      $obj = new Status;
      $obj->sta_name = $sta_name;
      $obj->sta_type = $sta_type;
      $obj->sta_flag_cuti = $sta_flag_cuti;
      $obj->sta_created_at = Helper::dateNowDB();

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

      $sta_id = $request->getParam('sta_id');
      $sta_name = $request->getParam('sta_name');
      $sta_type = $request->getParam('sta_type');
      $sta_flag_cuti = !empty($request->getParam('sta_flag_cuti')) ? $request->getParam('sta_flag_cuti') : 0;

      $obj = Status::find($sta_id);
      $obj->sta_name = $sta_name;
      $obj->sta_type = $sta_type;
      $obj->sta_flag_cuti = $sta_flag_cuti;
      $obj->sta_updated_at = Helper::dateNowDB();

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

      $sta_id = $request->getParam('sta_id');
      $obj = Status::find($sta_id);
      if(!empty($obj)) {
        $arrData['sta_id'] = $obj->sta_id;
        $arrData['sta_name'] = $obj->sta_name;
        $arrData['sta_flag_cuti'] = $obj->sta_flag_cuti;
        $arrData['sta_type'] = !empty($obj->sta_type) ? $obj->sta_type : '';
      }

      return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
      $arrData = array(
          'message' => '',
          'success' => false,
      );

      $sta_id = $request->getParam('sta_id');
      $obj = Status::find($sta_id);
      $obj->sta_void = 1;

      if($obj->save()) {
        $arrData['success'] = true;
        $arrData['message'] = 'Delete data success';
      } else {
        $arrData['message'] = 'Oops.. please try again!';
      }

      return $response->withJson($arrData);
    }
}
