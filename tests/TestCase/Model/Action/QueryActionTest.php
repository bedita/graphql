<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\GraphQL\Test\TestCase\Model\Action;

use BEdita\GraphQL\Model\Action\QueryAction;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\Action\QueryAction} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Action\QueryAction
 */
class QueryActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.streams',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Data provider for `testExecute`
     *
     * @return void
     */
    public function executeProvider(): array
    {
        return [
            'user' => [
                [
                    'data' => [
                        'user' => [
                            'username' => 'first user',
                        ],
                    ],
                ],
                '{user(id: "1") { username }}',
            ],
            'role' => [
                [
                    'data' => [
                        'role' => [
                            'name' => 'first role',
                        ],
                    ],
                ],
                '{role(id: "1") { name }}',
             ],
            'usersFilter' => [
                [
                    'data' => [
                        'users' => [
                            [
                                'username' => 'second user',
                            ],
                        ],
                    ],
                ],
                '{users(filter: {query: "second"}) { username }}',
            ],
            'streamsFilter' => [
                [
                    'data' => [
                        'streams' => [
                            [
                                'mime_type' => 'image/png',
                            ],
                        ],
                    ],
                ],
                '{streams(filter: {field_name: "file_name", field_value: "bedita_logo.png"}) { mime_type }}',
            ],
         ];
    }

    /**
     * Test simple query execution.
     *
     * @return void
     * @covers ::execute()
     * @covers ::buildSchema()
     * @covers \BEdita\GraphQL\Model\Type\ObjectsType::__construct()
     * @covers \BEdita\GraphQL\Model\Type\QueryType::__construct()
     * @covers \BEdita\GraphQL\Model\Type\ResourcesType::__construct()
     * @dataProvider executeProvider
     */
    public function testExecute($expected, $query): void
    {
        $action = new QueryAction();
        $result = $action(compact('query'));

        static::assertEquals($expected, $result);
    }
}
