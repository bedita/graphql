<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

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
    }
);
