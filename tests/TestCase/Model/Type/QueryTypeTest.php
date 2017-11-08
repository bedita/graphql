<?php
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
use Cake\Network\Exception\BadRequestException;
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testResolve`
     *
     * @return void
     */
    public function resolveProvider()
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
            'users' => [
                'users',
                null,
                [
                    0 => 'BEdita\Core\Model\Entity\User',
                    1 => 'BEdita\Core\Model\Entity\User',
                ],
            ],
            'roles' => [
                'roles',
                null,
                [0 => 'BEdita\Core\Model\Entity\Role'],
            ],
            'gustavo' => [
                'gustavo',
                null,
                new BadRequestException('Type name "gustavo" not found'),
            ],
         ];
    }

    /**
     * Test simple resource type creation
     *
     * @return void
     *
     * @covers ::__construct()
     * @covers ::resolve()
     * @covers ::resolveResource()
     * @covers ::resolveResourcesList()
     * @covers ::resolveObject()
     * @covers ::resolveObjectsList()
     * @dataProvider resolveProvider
     */
    public function testResolve($type, $args, $expected)
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
            if (is_numeric($key)) {
                static::assertEquals($value, get_class($result[$key]));
            } else {
                static::assertEquals($value, $result[$key]);
            }
        }
    }
}
