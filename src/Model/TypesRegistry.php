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
    private static $resourceTypes = [];
    private static $objectTypes = [];
    private static $query;

    /**
     * Resource type names registry
     *
     * @var array
     */
    private static $resourceTypeNames = ['roles', 'applications'];

    /**
     * Object type names registry
     *
     * @var array
     */
    private static $objectTypeNames = [];

    /**
     * Return all resource and object types
     *
     * @return array
     */
    public static function rootTypes()
    {
        $types = [];

        $resources = static::resourceTypeNames();
        foreach ($resources as $name) {
            $types[$name] = [
                'type' => static::resourceType($name),
                'description' => sprintf('Get "%s" item by id', $name),
                'args' => [
                    'id' => static::nonNull(static::id())
                ],
            ];
        }

        $objects = static::objectTypeNames();
        foreach ($objects as $name) {
            $types[$name] = [
                'type' => static::objectType($name),
                'description' => sprintf('Get "%s" item by id', $name),
                'args' => [
                    'id' => static::nonNull(static::id())
                ],
            ];
        }

        return $types;
    }

    /**
     * Registered resource type names
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
     *
     * @return array
     */
    public static function objectTypeNames()
    {
        if (empty(self::$objectTypeNames)) {
            self::$objectTypeNames = TableRegistry::get('ObjectTypes')->find('list', ['valueField' => 'name'])->toArray();
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
        if (empty(self::$resourceType[$name])) {
            $resType = new ResourcesType(compact('name'));
            self::$resourceTypes[$name] = $resType;
        }

        return self::$resourceTypes[$name];
    }

    /**
     * See if $name is a registered object type
     *
     * @param string $name Type name
     * @return bool True if item `name` is an `object` type
     */
    public static function isAnObject($name)
    {
        return in_array($name, static::objectTypeNames());
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
