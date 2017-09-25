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

namespace BEdita\GraphQL\Test\TestCase\Model;

use BEdita\GraphQL\Model\FieldsRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\FieldsRegistry} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\FieldsRegistry
 */
class FieldsRegistryTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
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
    * Data provider for `testObjectFields`
    *
    * @return void
    */
    public function objectsProvider()
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
     * Test `objectFields` method
     *
     * @return void
     *
     * @covers ::objectFields()
     * @covers ::objectProperties()
     * @covers ::clear()
     *
     * @dataProvider objectsProvider
     */
    public function testObjectFields($type, $expected)
    {
        FieldsRegistry::clear();
        $result = FieldsRegistry::objectFields($type);

        static::assertNotEmpty($result);
        foreach ($expected as $value) {
            static::assertArrayHasKey($value, $result);
        }
    }

   /**
    * Data provider for `testResourceFields`
    *
    * @return void
    */
    public function resourcesProvider()
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
     * Test `resourceFields` method
     *
     * @return void
     *
     * @covers ::resourceFields()
     * @covers ::resourceProperties()
     * @covers ::clear()
     *
     * @dataProvider resourcesProvider
     */
    public function testResourceFields($type, $expected)
    {
        FieldsRegistry::clear();
        $result = FieldsRegistry::resourceFields($type);

        static::assertNotEmpty($result);
        foreach ($expected as $value) {
            static::assertArrayHasKey($value, $result);
        }
    }
}
