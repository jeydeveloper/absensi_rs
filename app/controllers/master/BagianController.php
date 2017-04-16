<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;

class BagianController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /bagian/list' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'bagian/list.phtml', $data);
    }

    public function add($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /bagian/add' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'bagian/add.phtml', $data);
    }

    public function edit($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /bagian/edit' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $ptn_id = $request->getParam('ptn_id');
        $data['ptn_id'] = $ptn_id;

        return $this->ci->get('renderer')->render($response, 'bagian/edit.phtml', $data);
    }
}
