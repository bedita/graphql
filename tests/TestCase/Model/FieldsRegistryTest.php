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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Data provider for `testObjectFields`
     *
     * @return array
     */
    public function objectsProvider(): array
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
     * @param string $type Object type name
     * @param array $expected Expected fields
     * @return void
     * @dataProvider objectsProvider
     * @covers ::objectFields()
     * @covers ::objectProperties()
     * @covers ::clear()
     */
    public function testObjectFields($type, $expected): void
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
     * @return array
     */
    public function resourcesProvider(): array
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
     * @covers ::resourceFields()
     * @covers ::resourceProperties()
     * @covers ::clear()
     * @dataProvider resourcesProvider
     */
    public function testResourceFields($type, $expected): void
    {
        FieldsRegistry::clear();
        $result = FieldsRegistry::resourceFields($type);

        static::assertNotEmpty($result);
        foreach ($expected as $value) {
            static::assertArrayHasKey($value, $result);
        }
    }

    /**
     * Test `inputFilterFields`
     *
     * @return void
     * @covers ::inputFilterFields()
     * @covers ::filterProperties()
     * @covers ::clear()
     */
    public function testInputTypeFields(): void
    {
        FieldsRegistry::clear();
        $fields = FieldsRegistry::inputFilterFields();

        $expected = [
            'field_name',
            'field_value',
            'query',
        ];

        static::assertNotEmpty($fields);
        foreach ($expected as $key) {
            static::assertArrayHasKey($key, $fields);
        }
    }
}
