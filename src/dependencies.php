<?php
// DIC configuration

$container = $app->getContainer();

// error 404
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(array('error' => 'Page not found'));
    };
};

// error 405
$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'application/json')
            ->withJson(array('error' => 'Method must be one of: ' . implode(', ', $methods)));
    };
};

// error 500
/*
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(array('error' => 'Something went wrong!'));
    };
};
*/

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Service factory for the ORM
$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['HomeController'] = function($c) {
    return new App\Controllers\HomeController($c);
};

$container['UserApi'] = function($c) {
    return new App\Api\UserApi($c);
};

$container['BagianController'] = function($c) {
    return new App\Controllers\Master\BagianController($c);
};

$container['BagianApi'] = function($c) {
    return new App\Api\BagianApi($c);
};

$container['StatusController'] = function($c) {
    return new App\Controllers\Master\StatusController($c);
};

$container['StatusApi'] = function($c) {
    return new App\Api\StatusApi($c);
};

$container['JabatanController'] = function($c) {
    return new App\Controllers\Master\JabatanController($c);
};

$container['JabatanApi'] = function($c) {
    return new App\Api\JabatanApi($c);
};

$container['UnitController'] = function($c) {
    return new App\Controllers\Master\UnitController($c);
};

$container['UnitApi'] = function($c) {
    return new App\Api\UnitApi($c);
};

$container['EmployeeController'] = function($c) {
    return new App\Controllers\Master\EmployeeController($c);
};

$container['EmployeeApi'] = function($c) {
    return new App\Api\EmployeeApi($c);
};

$container['HolidayController'] = function($c) {
    return new App\Controllers\Attendance\HolidayController($c);
};

$container['HolidayApi'] = function($c) {
    return new App\Api\HolidayApi($c);
};

$container['CutiController'] = function($c) {
    return new App\Controllers\Report\CutiController($c);
};

$container['CutiApi'] = function($c) {
    return new App\Api\CutiApi($c);
};

$container['IzinController'] = function($c) {
    return new App\Controllers\Attendance\IzinController($c);
};

$container['IzinApi'] = function($c) {
    return new App\Api\IzinApi($c);
};

$container['OvertimeController'] = function($c) {
    return new App\Controllers\Attendance\OvertimeController($c);
};

$container['OvertimeApi'] = function($c) {
    return new App\Api\OvertimeApi($c);
};

$container['ScheduleController'] = function($c) {
    return new App\Controllers\Attendance\ScheduleController($c);
};

$container['ScheduleApi'] = function($c) {
    return new App\Api\ScheduleApi($c);
};

$container['SessionApi'] = function($c) {
    return new App\Api\SessionApi($c);
};

$container['JadwalkerjaController'] = function($c) {
    return new App\Controllers\Proses\JadwalkerjaController($c);
};

$container['JadwalkerjaApi'] = function($c) {
    return new App\Api\JadwalkerjaApi($c);
};

$container['SettingController'] = function($c) {
    return new App\Controllers\Master\SettingController($c);
};

$container['SettingApi'] = function($c) {
    return new App\Api\SettingApi($c);
};

$container['MappingjadwalController'] = function($c) {
    return new App\Controllers\Proses\MappingjadwalController($c);
};

$container['MappingjadwalApi'] = function($c) {
    return new App\Api\MappingjadwalApi($c);
};

$container['ReportabsenceController'] = function($c) {
    return new App\Controllers\Report\ReportabsenceController($c);
};

$container['RoleaccessController'] = function($c) {
    return new App\Controllers\Master\RoleaccessController($c);
};

$container['RoleaccessApi'] = function($c) {
    return new App\Api\RoleaccessApi($c);
};

$container['UseradminController'] = function($c) {
    return new App\Controllers\Master\UseradminController($c);
};

$container['UseradminApi'] = function($c) {
    return new App\Api\UseradminApi($c);
};

$container['ReportApi'] = function($c) {
    return new App\Api\ReportApi($c);
};
