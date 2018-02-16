<?php
use Slim\Exception\NotFoundException;
// Application middleware

// Return connection database
$app->getContainer()->get("db");

// Check the user is logged in when necessary.
$loggedInMiddleware = function ($request, $response, $next) {
    $route = $request->getAttribute('route');

    // return NotFound for non existent route
    if (empty($route)) {
        throw new NotFoundException($request, $response);
    }

    $routeName = $route->getName();
    $groups = $route->getGroups();
    $methods = $route->getMethods();
    $arguments = $route->getArguments();

    # Define routes that user does not have to be logged in with. All other routes, the user
    # needs to be logged in with.
    $publicRoutesArray = array(
        'login',
        'post-login',
        'post-api-session-check',
    );

    $publicRoutesArray2 = array(
        'change-password',
        'post-change-password',
        'post-api-session-check',
        'logout',
    );

    $publicRoutesArray3 = array(
        'reportabsence-formindividual',
        'post-change-password',
        'post-api-session-check',
        'logout',
        'reportabsence-list',
        'reportabsence-listyearly',
        'employee-list',
        'employee-edit',
        'api-employee-lists',
        'api-employee-edit',
        'post-api-employee-edit',
    );

    if (!isset($_SESSION['USERID']) && !in_array($routeName, $publicRoutesArray))
    {
        $response = $response->withRedirect($this->get('settings')['baseUrl'] . 'login');
    }
    elseif(isset($_SESSION['GUEST']) && $_SESSION['GUEST'] == 2 && !in_array($routeName, $publicRoutesArray3))
    {
        $response = $response->withRedirect($this->get('settings')['baseUrl'] . 'report/form-individual');
    }
    elseif(isset($_SESSION['GUEST']) && $_SESSION['GUEST'] == 1 && !in_array($routeName, $publicRoutesArray2))
    {
        $response = $response->withRedirect($this->get('settings')['baseUrl'] . 'change-password');
    }
    elseif(isset($_SESSION['USERID']) && in_array($routeName, $publicRoutesArray))
    {
        $response = $response->withRedirect($this->get('settings')['baseUrl'] . 'home');
    }
    else
    {
        // Proceed as normal...
        $response = $next($request, $response);
    }

    return $response;
};

// Apply the middleware to every request.
$app->add($loggedInMiddleware);
