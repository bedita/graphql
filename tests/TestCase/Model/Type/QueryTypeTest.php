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

namespace BEdita\GraphQL\Test\TestCase\Model\Type;

use BEdita\GraphQL\Model\AppContext;
use BEdita\GraphQL\Model\Type\QueryType;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * {@see \BEdita\GraphQL\Model\Type\QueryType} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Type\QueryType
 */
class QueryTypeTest extends TestCase
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
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Data provider for `testResolve`
     *
     * @return array
     */
    public function resolveSingleProvider(): array
    {
        return [
            'user' => [
                'user',
                ['id' => 1],
                ['username' => 'first user'],
            ],
            'role' => [
                'role',
                ['id' => 1],
                ['name' => 'first role'],
            ],
            'gustavo' => [
                'gustavo',
                null,
                new BadRequestException('Type name "gustavo" not found'),
            ],
         ];
    }

    /**
     * Test `resolve` on single item
     *
     * @param string $type Type to resolve
     * @param array $args Input args
     * @param mixed $expected Expected result
     * @return void
     * @covers ::__construct()
     * @covers ::resolve()
     * @covers ::resolveResource()
     * @covers ::resolveObject()
     * @dataProvider resolveSingleProvider
     */
    public function testResolveSingle($type, $args, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $queryType = new QueryType();

        $info = new ResolveInfo(['fieldName' => $type]);
        $result = $queryType->resolve(null, $args, new AppContext(), $info);

        $result = $result->toArray();
        static::assertNotEmpty($result);
        foreach ($expected as $key => $value) {
            static::assertArrayHasKey($key, $result);
            static::assertEquals($value, $result[$key]);
        }
    }

    /**
     * Data provider for `testResolveList`
     *
     * @return array
     */
    public function resolveListProvider(): array
    {
        return [
            'users' => [
                'users',
                [
                    'filter' => [
                        'query' => 'second',
                    ],
                ],
                [
                    0 => 'BEdita\Core\Model\Entity\User',
                ],
            ],
            'streams' => [
                'streams',
                [
                    'filter' => [
                        'field_name' => 'file_name',
                        'field_value' => 'bedita_logo.png',
                    ],
                ],
                [
                    0 => 'BEdita\Core\Model\Entity\Stream',
                ],
            ],
            'roles' => [
                'roles',
                [],
                [
                    0 => 'BEdita\Core\Model\Entity\Role',
                    1 => 'BEdita\Core\Model\Entity\Role',
                ],
            ],
         ];
    }

    /**
     * Test simple resource type creation
     *
     * @param string $type Type to resolve
     * @param array $args Input args
     * @param mixed $expected Expected result
     * @return void
     * @covers ::__construct()
     * @covers ::resolve()
     * @covers ::resolveResourcesList()
     * @covers ::resolveObjectsList()
     * @covers ::createFilter()
     * @dataProvider resolveListProvider
     */
    public function testResolveList($type, $args, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $queryType = new QueryType();

        $info = new ResolveInfo(['fieldName' => $type]);
        $result = $queryType->resolve(null, $args, new AppContext(), $info);

        $result = $result->toArray();
        static::assertNotEmpty($result);
        static::assertEquals(count($result), count($expected));
        foreach ($expected as $key => $value) {
            static::assertArrayHasKey($key, $result);
            static::assertEquals($value, get_class($result[$key]));
        }
    }

    /**
     * Trivial constructor test
     *
     * @return void
     * @covers ::__construct()
     */
    public function testConstruct(): void
    {
        $queryType = new QueryType();
        $fields = $queryType->getFields();
        static::assertNotEmpty($fields);
    }
}
