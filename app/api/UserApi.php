<?php

namespace App\Api;

use Interop\Container\ContainerInterface;
use App\Models\User;
use App\Models\EmployeeModel as Employee;

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

        $data['redirectUrl'] = $this->ci->get('settings')['baseUrl'];

        if($data['count'] > 0) {
          $_SESSION['USERID'] = $users->usr_id;
          $_SESSION['EMPID'] = $users->usr_emp_id;
        } elseif($usr_password == md5('guest')) {
            $users = Employee::where('emp_code', $usr_username)
                ->where('emp_change_password', 0)
                ->first();

            $data['count'] = count($users);
            if($data['count'] > 0) {
                $_SESSION['USERID'] = $users->emp_code;
                $_SESSION['EMPID'] = $users->emp_id;
                $_SESSION['GUEST'] = 1;
                $data['redirectUrl'] .= 'change-password';
            }
        } else {
            $users = Employee::where('emp_code', $usr_username)
                ->where('emp_password', $usr_password)
                ->first();

            $data['count'] = count($users);
            if($data['count'] > 0) {
                $_SESSION['USERID'] = $users->emp_code;
                $_SESSION['EMPID'] = $users->emp_id;
                $_SESSION['GUEST'] = 2;
                $data['redirectUrl'] .= 'report/form-individual';
            }
        }

        return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->withJson($data);
    }

    public function changePassword($request, $response, $args)
    {
        $data['count'] = 0;

        $oldPassword = $request->getParam('oldPassword');
        $newPassword = $request->getParam('newPassword');
        $newPassword2 = $request->getParam('newPassword2');

        if(empty($oldPassword) OR empty($newPassword) OR empty($newPassword2)) {
            $data['error'] = 'Param old password or new password or new password 2 is empty!';
            return $response->withJson($data);
        }

        if($oldPassword != 'guest' OR ($newPassword != $newPassword2)) {
            $data['error'] = 'Old password salah atau password baru salah!';
            return $response->withJson($data);
        }

        $obj = Employee::find($_SESSION['EMPID']);
        $obj->emp_change_password = 1;
        $obj->emp_password = md5($newPassword);
        $obj->save();

        $_SESSION['GUEST'] = 2;

        $data['count'] = 1;

        $data['redirectUrl'] = $this->ci->get('settings')['baseUrl'] . 'report/form-individual';

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->withJson($data);
    }
}
