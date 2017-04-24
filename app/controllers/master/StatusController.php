<?php

namespace App\Controllers\Master;

use Interop\Container\ContainerInterface;
use Gettext\Translator;

class StatusController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'sta_id';
        $this->data['inputFocus'] = 'sta_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /master/status/list' route");

        return $this->ci->get('renderer')->render($response, 'master/status/list.phtml', $this->data);
    }
}
