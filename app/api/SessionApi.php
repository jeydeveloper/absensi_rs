<?php

namespace App\Api;

use Interop\Container\ContainerInterface;

class SessionApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function doCheck($request, $response, $args)
    {
      $arrData['loggedIn'] = 1;

      if( !isset( $_SESSION['USERID'] )) $arrData['loggedIn'] = 0;

      return $response->withJson($arrData);
    }
}
