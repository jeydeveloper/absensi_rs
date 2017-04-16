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
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(array('error' => 'Something went wrong!'));
    };
};

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

$container['PatientController'] = function($c) {
    return new App\Controllers\PatientController($c);
};

$container['MedicineController'] = function($c) {
    return new App\Controllers\MedicineController($c);
};

$container['VisitController'] = function($c) {
    return new App\Controllers\VisitController($c);
};

$container['PatientApi'] = function($c) {
    return new App\Api\PatientApi($c);
};
