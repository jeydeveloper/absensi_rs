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
        'post-login'
    );

    if (!isset($_SESSION['USERID']) && !in_array($routeName, $publicRoutesArray))
    {
        $response = $response->withRedirect($this->get('settings')['baseUrl'] . 'login');
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
