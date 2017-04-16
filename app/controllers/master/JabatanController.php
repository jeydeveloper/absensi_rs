<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;

class JabatanController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /jabatan/list' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'jabatan/list.phtml', $data);
    }

    public function add($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /jabatan/add' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        return $this->ci->get('renderer')->render($response, 'jabatan/add.phtml', $data);
    }

    public function edit($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /jabatan/edit' route");

        $data['baseUrl'] = $this->ci->get('settings')['baseUrl'];

        $ptn_id = $request->getParam('ptn_id');
        $data['ptn_id'] = $ptn_id;

        return $this->ci->get('renderer')->render($response, 'jabatan/edit.phtml', $data);
    }
}
