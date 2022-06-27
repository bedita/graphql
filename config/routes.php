<?php
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes) {
    $routes->plugin(
        'BEdita/GraphQL',
        ['path' => '/','_namePrefix' => 'api:'],
        function (RouteBuilder $routes) {
            // Execute GraphQL query.
            $routes->connect(
                '/graphql',
                ['controller' => 'GraphQL', 'action' => 'execute']
            );
        }
    );
};
