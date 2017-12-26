<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\RoleaccessModel as Roleaccess;
use App\Helper;

class RoleaccessApi
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

        $result = Roleaccess::getAllNonVoid();
        if (!empty($result)) {
            $tmp = $this->ci->get('settings')['dataStatic']['listModulePrivilege'];
            foreach ($result as $key => $value) {
                if(!empty($value->role_privilege)) {
                    $tmp2 = explode(',', $value->role_privilege);
                    $tmp3 = [];
                    foreach ($tmp2 as $value2) {
                        $tmp3[] = !empty($tmp[$value2]) ? ('['.$tmp[$value2].']') : '-';
                    }
                    if(!empty($tmp3)) $value->role_privilege = join(', ', $tmp3);
                }
                $arrData['data'][] = array(
                    ($key + 1),
                    $value->role_id,
                    $value->role_name,
                    $value->role_privilege,
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

        $role_name = $request->getParam('role_name');
        $role_privilege = $request->getParam('role_privilege');
        $role_privilege = join(',', $role_privilege);

        $obj = new Roleaccess;
        $obj->role_name = $role_name;
        $obj->role_privilege = $role_privilege;
        $obj->role_created_at = Helper::dateNowDB();

        if ($obj->save()) {
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

        $role_id = $request->getParam('role_id');
        $role_name = $request->getParam('role_name');
        $role_privilege = $request->getParam('role_privilege');
        $role_privilege = join(',', $role_privilege);

        $obj = Roleaccess::find($role_id);
        $obj->role_name = $role_name;
        $obj->role_privilege = $role_privilege;
        $obj->role_updated_at = Helper::dateNowDB();

        if ($obj->save()) {
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

        $role_id = $request->getParam('role_id');
        $obj = Roleaccess::find($role_id);
        if (!empty($obj)) {
            $arrData['role_id'] = $obj->role_id;
            $arrData['role_name'] = $obj->role_name;
            $arrData['role_privilege'] = !empty($obj->role_privilege) ? explode(',', $obj->role_privilege) : [];
        }

        return $response->withJson($arrData);
    }

    public function doDelete($request, $response, $args)
    {
        $arrData = array(
            'message' => '',
            'success' => false,
        );

        $role_id = $request->getParam('role_id');
        $obj = Roleaccess::find($role_id);
        $obj->role_void = 1;

        if ($obj->save()) {
            $arrData['success'] = true;
            $arrData['message'] = 'Delete data success';
        } else {
            $arrData['message'] = 'Oops.. please try again!';
        }

        return $response->withJson($arrData);
    }
}
