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
        'plugin.BEdita/Core.streams',
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
            'document',
            'documents',
            'event',
            'events',
            'file',
            'files',
            'folder',
            'folders',
            'location',
            'locations',
            'media',
            'media_item',
            'news',
            'news_item',
            'object',
            'objects',
            'profile',
            'profiles',
            'role',
            'roles',
            'stream',
            'streams',
            'user',
            'users',
        ];

        ksort($result);
        static::assertNotEmpty($result);
        static::assertEquals(array_keys($result), $expected);
        foreach ($expected as $k) {
            static::assertArrayHasKey('type', $result[$k]);
            static::assertArrayHasKey('description', $result[$k]);
            static::assertArrayHasKey('args', $result[$k]);
        }
    }

    /**
     * Data provider for `testInspectTypeName`
     *
     * @return array
     */
    public function inspectProvider()
    {
        return [
            'user' => [
                'user',
                TypesRegistry::SINGLE_OBJECT,
            ],
            'users' => [
                'users',
                TypesRegistry::OBJECTS_LIST,
            ],
            'role' => [
                'role',
                TypesRegistry::SINGLE_RESOURCE,
            ],
            'roles' => [
                'roles',
                TypesRegistry::RESOURCES_LIST,
            ],
            'gustavo' => [
                'gustavo',
                false,
            ],
         ];
    }

    /**
     * Test `inspectTypeName` method
     *
     * @param string $name Type name
     * @param mixed $expected Inspection result
     * @return void
     *
     * @dataProvider inspectProvider
     * @covers ::inspectTypeName()
     */
    public function testInspectTypeName($name, $expected)
    {
        $result = TypesRegistry::inspectTypeName($name);
        static::assertEquals($result, $expected);
    }

    /**
     * Data provider for `testPropertySchema`
     *
     * @return void
     */
    public function propertySchemaProvider()
    {
        return [
            'date' => [
                [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
                TypesRegistry::dateTime(),
            ],
            'int' => [
                [
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'integer',
                        ]
                    ],
                ],
                TypesRegistry::int(),
            ],
            'bool' => [
                [
                    'oneOf' => [
                        [
                            'type' => 'boolean',
                        ],
                        [
                            'type' => 'null',
                        ]
                    ],
                ],
                TypesRegistry::boolean(),
            ],
            'float' => [
                [
                    'type' => 'number',
                ],
                TypesRegistry::float(),
            ],
            'text' => [
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                TypesRegistry::string(),
            ],
            'unknown' => [
                [
                    'type' => 'sometype',
                ],
                TypesRegistry::string(),
            ],
            'moreunknown' => [
                [
                    'unknown' => 'weirdtype',
                ],
                TypesRegistry::string(),
            ],
        ];
    }

    /**
     * Test `fromPropertySchema` method
     *
     * @return void
     *
     * @param array $schema Property JSON schema
     * @param mixed $expected Expected property type
     * @covers ::fromPropertySchema()
     * @covers ::propertyFromTypeSchema()
     * @dataProvider propertySchemaProvider
     */
    public function testPropertySchema(array $schema, $expected)
    {
        $result = TypesRegistry::fromPropertySchema($schema);
        static::assertEquals($result, $expected);
    }
}
