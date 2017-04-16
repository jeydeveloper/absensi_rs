<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;

class StatusController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /status/list' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'status/list.phtml', $data);
    }

    public function add($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /status/add' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'status/add.phtml', $data);
    }

    public function edit($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /status/edit' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $ptn_id = $request->getParam('ptn_id');
        $data['ptn_id'] = $ptn_id;

        return $this->ci->get('renderer')->render($response, 'status/edit.phtml', $data);
    }
}
