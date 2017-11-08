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

namespace BEdita\GraphQL\Model;

use BEdita\GraphQL\Model\Type\ObjectsType;
use BEdita\GraphQL\Model\Type\QueryType;
use BEdita\GraphQL\Model\Type\ResourcesType;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * TypesRegistry: registry and factory for a BEdita project types.
 *
 * @package BEdita\GraphQL\Model
 */
class TypesRegistry
{
    /**
     * Used to identify a single resource.
     *
     * @var int
     */
    const SINGLE_RESOURCE = 1;

    /**
     * Used to identify a list of resources.
     *
     * @var int
     */
    const RESOURCES_LIST = 2;

    /**
     * Used to identify a singular object.
     *
     * @var int
     */
    const SINGLE_OBJECT = 3;

    /**
     * Used to identify an objects list.
     *
     * @var int
     */
    const OBJECTS_LIST = 4;

    /**
     * Resource types internal registry
     *
     * @var array
     */
    private static $resourceTypes = [];

    /**
     * Object types internal registry
     *
     * @var array
     */
    private static $objectTypes = [];

    /**
     * Query type
     *
     * @var \BEdita\GraphQL\Model\Type\QueryType
     */
    private static $query;

    /**
     * Resource type names registry
     * Format: {plural_name} => {singular_name}
     *
     * @var array
     */
    private static $resourceTypeNames = [
        'roles' => 'role',
        'applications' => 'application',
    ];

    /**
     * Object type names registry
     *
     * @var array
     */
    private static $objectTypeNames = [];

    /**
     * Clear internal dynamic registry
     *
     * @return void
     */
    public static function clear()
    {
        self::$resourceTypes = [];
        self::$objectTypes = [];
        self::$objectTypeNames = [];
    }

    /**
     * Return all resource and object types
     *
     * @return array
     */
    public static function rootTypes()
    {
        $types = [];

        $resources = static::resourceTypeNames();
        foreach ($resources as $name => $singular) {
            $types[$singular] = [
                'type' => static::resourceType($name),
                'description' => sprintf('Get "%s" item by id', $singular),
                'args' => [
                    'id' => static::nonNull(static::id())
                ],
            ];
            $types[$name] = [
                'type' => static::listOf(static::resourceType($name)),
                'description' => sprintf('Get list of "%s"', $name),
                'args' => [
                ],
            ];
        }

        $objects = static::objectTypeNames();
        foreach ($objects as $name => $singular) {
            $types[$singular] = [
                'type' => static::objectType($name),
                'description' => sprintf('Get "%s" item by id', $singular),
                'args' => [
                    'id' => static::nonNull(static::id())
                ],
            ];
            $types[$name] = [
                'type' => static::listOf(static::objectType($name)),
                'description' => sprintf('Get list of "%s"', $name),
                'args' => [
                ],
            ];
        }

        return $types;
    }

    /**
     * Registered resource type names.
     * Format: '{plural_name}' => '{singular_name}'
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public static function resourceTypeNames()
    {
        return self::$resourceTypeNames;
    }

    /**
     * Registered object type names
     * Format: '{plural_name}' => '{singular_name}'
     *
     * @return array
     */
    public static function objectTypeNames()
    {
        if (empty(self::$objectTypeNames)) {
            $objectTypes = TableRegistry::get('ObjectTypes')->find('all', ['fields' => ['name', 'singular']])->toArray();
            foreach ($objectTypes as $data) {
                self::$objectTypeNames[$data['name']] = $data['singular'];
            }
        }

        return self::$objectTypeNames;
    }

    /**
     * Get graphql representation of an object from its type $name
     *
     * @param string $name Object type name
     * @return \BEdita\GraphQL\Model\Type\ObjectsType
     */
    public static function objectType($name)
    {
        if (empty(self::$objectTypes[$name])) {
            $objType = new ObjectsType(compact('name'));
            self::$objectTypes[$name] = $objType;
        }

        return self::$objectTypes[$name];
    }

    /**
     * Get graphql representation of a resource from its type $name
     *
     * @param string $name Object type name
     * @return \BEdita\GraphQL\Model\Type\ResourcesType
     */
    public static function resourceType($name)
    {
        if (empty(self::$resourceTypes[$name])) {
            $resType = new ResourcesType(compact('name'));
            self::$resourceTypes[$name] = $resType;
        }

        return self::$resourceTypes[$name];
    }

    /**
     * See if $name refers to:
     *  - a single object
     *  - a list of objects
     *  - a single resource
     *  - a list of resources
     *
     * @param string $name Name to inspect
     * @return int|bool Corresponding constant if a match is found, false otherwise
     */
    public static function inspectTypeName($name)
    {
        if (array_key_exists($name, static::objectTypeNames())) {
            return static::OBJECTS_LIST;
        } elseif (array_key_exists($name, static::resourceTypeNames())) {
            return static::RESOURCES_LIST;
        } elseif (in_array($name, static::resourceTypeNames())) {
            return static::SINGLE_RESOURCE;
        } elseif (in_array($name, static::objectTypeNames())) {
            return static::SINGLE_OBJECT;
        }

        return false;
    }

    /**
     * @return \BEdita\GraphQL\Model\Type\QueryType
     * @codeCoverageIgnore
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }

    /**
     * @return \GraphQL\Type\Definition\BooleanType
     * @codeCoverageIgnore
     */
    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     * @codeCoverageIgnore
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     * @codeCoverageIgnore
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     * @codeCoverageIgnore
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     * @codeCoverageIgnore
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type Type name
     * @return ListOfType
     * @codeCoverageIgnore
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type Type name
     * @return NonNull
     * @codeCoverageIgnore
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
