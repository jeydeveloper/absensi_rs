<?php

namespace App\Controllers\Attendance;

use Interop\Container\ContainerInterface;
use Gettext\Translator;

class OvertimeController extends \App\Controllers\BaseController
{
    protected $ci;
    protected $data;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct();
        $this->ci = $ci;

        $this->data['primaryKey'] = 'emov_id';
        $this->data['inputFocus'] = 'emov_name';
        $this->data['baseUrl'] = $this->ci->get('settings')['baseUrl'];
    }

    public function lists($request, $response, $args)
    {
        $this->ci->get('logger')->info("Slim-Skeleton 'GET /attendance/overtime/list' route");

        return $this->ci->get('renderer')->render($response, 'attendance/overtime/list.phtml', $this->data);
    }
}
