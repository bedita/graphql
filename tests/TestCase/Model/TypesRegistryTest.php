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

use BEdita\GraphQL\Model\TypesRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\TypesRegistry} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\TypesRegistry
 */
class TypesRegistryTest extends TestCase
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
     * Test `rootTypes` method
     *
     * @return void
     *
     * @covers ::rootTypes()
     * @covers ::objectTypeNames()
     * @covers ::objectType()
     * @covers ::resourceTypeNames()
     * @covers ::resourceType()
     * @covers ::clear()
     */
    public function testRootTypes()
    {
        TypesRegistry::clear();
        $result = TypesRegistry::rootTypes();

        $expected = [
            'roles',
            'applications',
            'documents',
            'events',
            'locations',
            'media',
            'news',
            'profiles',
            'users',
        ];

        static::assertNotEmpty($result);
        static::assertEquals(array_keys($result), $expected);
        foreach ($expected as $k) {
            static::assertArrayHasKey('type', $result[$k]);
            static::assertArrayHasKey('description', $result[$k]);
            static::assertArrayHasKey('args', $result[$k]);
        }
    }

    /**
     * Test `isAnObject` method
     *
     * @return void
     *
     * @covers ::isAnObject()
     */
    public function testIsAnObject()
    {
        $result = TypesRegistry::isAnObject('documents');
        static::assertTrue($result);

        $result = TypesRegistry::isAnObject('roles');
        static::assertFalse($result);
    }
}
