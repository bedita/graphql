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

use BEdita\GraphQL\Model\Type\ResourcesType;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\Type\ResourcesType} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Type\ResourcesType
 */
class ResourcesTypeTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * Data provider for `testCreate`
     *
     * @return array
     */
    public function typeProvider(): array
    {
        return [
           'roles' => [
               'roles',
               ['name', 'description', 'unchangeable'],
           ],
           'applications' => [
                'applications',
                ['api_key', 'name', 'description'],
            ],
        ];
    }

    /**
     * Test simple resource type creation
     *
     * @return void
     * @covers ::__construct()
     * @dataProvider typeProvider
     */
    public function testCreate($name, $expected): void
    {
        $resourceType = new ResourcesType(compact('name'));

        $fields = $resourceType->getFields();

        static::assertNotEmpty($fields);
        foreach ($expected as $key) {
            static::assertArrayHasKey($key, $fields);
        }
    }
}
