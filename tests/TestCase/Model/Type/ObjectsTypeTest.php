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

use BEdita\GraphQL\Model\Type\ObjectsType;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\Type\ObjectsType} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Type\ObjectsType
 */
class ObjectsTypeTest extends TestCase
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
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testCreate`
     *
     * @return void
     */
    public function typeProvider()
    {
        return [
            'users' => [
                'users',
                ['username', 'verified'],
            ],
            'documents' => [
                'documents',
                ['title', 'description', 'body'],
            ],
        ];
    }

    /**
     * Test simple object type creation
     *
     * @return void
     *
     * @covers ::__construct()
     * @dataProvider typeProvider
     */
    public function testCreate($name, $expected)
    {
        $objectType = new ObjectsType(compact('name'));

        $fields = $objectType->getFields();

        static::assertNotEmpty($fields);
        foreach ($expected as $key) {
            static::assertArrayHasKey($key, $fields);
        }
    }
}
