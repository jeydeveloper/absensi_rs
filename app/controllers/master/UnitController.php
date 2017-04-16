<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;

class UnitController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /unit/list' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'unit/list.phtml', $data);
    }

    public function add($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /unit/add' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'unit/add.phtml', $data);
    }

    public function edit($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /unit/edit' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $ptn_id = $request->getParam('ptn_id');
        $data['ptn_id'] = $ptn_id;

        return $this->ci->get('renderer')->render($response, 'unit/edit.phtml', $data);
    }
}
