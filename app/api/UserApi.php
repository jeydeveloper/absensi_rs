<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\User;

class UserApi
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function lists($request, $response, $args)
    {
        $users = User::getAll();

        return $response->withJson($users);
    }

    public function login($request, $response, $args)
    {
        $data['count'] = 0;

        $usr_username = $request->getParam('usr_username');
        $usr_password = $request->getParam('usr_password');

        if(empty($usr_username) OR empty($usr_password)) {
          $data['error'] = 'Param usr_username or usr_password is empty!';
          return $response->withJson($data);
        }

        $usr_password = md5($usr_password);

        $users = User::where('usr_username', $usr_username)
        ->where('usr_password', $usr_password)
        ->first();

        $data['count'] = count($users);

        if($data['count'] > 0) {
          $_SESSION['USERID'] = $users->usr_id;
          $_SESSION['EMPID'] = $users->usr_emp_id;
        }

        return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->withJson($data);
    }
}
