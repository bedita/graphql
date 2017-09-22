<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'BEdita/GraphQL',
    [
        'path' => '/',
    ],
    function (RouteBuilder $routes) {
        // Execute GraphQL query.
        $routes->connect(
            '/graphql',
            ['controller' => 'GraphQL', 'action' => 'execute']
        );

        $routes->fallbacks(DashedRoute::class);
    }
);
