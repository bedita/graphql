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

namespace BEdita\GraphQL\Model;

use BEdita\Core\Model\Entity\StaticProperty;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * FieldsRegistry: registry and factory for resources and objects fields.
 *
 * @package BEdita\GraphQL\Model
 */
class FieldsRegistry
{
    /**
     * Cache config name used (object types).
     *
     * @var string
     */
    public const CACHE_CONFIG = '_bedita_object_types_';

    /**
     * Resource fields internal registry
     *
     * @var array
     */
    private static $resourceFields = [];

    /**
     * Object fields internal registry
     *
     * @var array
     */
    private static $objectFields = [];

    /**
     * Input filter fields internal registry
     *
     * @var array
     */
    private static $filterFields = [];

    /**
     * Clear internal registry
     *
     * @return void
     */
    public static function clear()
    {
        self::$resourceFields = [];
        self::$objectFields = [];
        self::$filterFields = [];
    }

    /**
     * Retrieve a list of fields for a given object type $name
     *
     * @param string $name Object type name
     * @return array
     */
    public static function objectFields($name)
    {
        if (empty(self::$objectFields[$name])) {
            $fields = [];
            $properties = static::objectProperties($name);
            foreach ($properties as $prop) {
                $fields[$prop->get('name')] = TypesRegistry::fromPropertySchema($prop->getSchema());
            }
            self::$objectFields[$name] = $fields;
        }

        return self::$objectFields[$name];
    }

    /**
     * Retrieve a list of fields for a given resource type $name
     *
     * @param string $name Resource type name
     * @return array
     */
    public static function resourceFields($name)
    {
        if (empty(self::$resourceFields[$name])) {
            $fields = [];
            $properties = static::resourceProperties($name);
            foreach ($properties as $prop) {
                $fields[$prop->get('name')] = TypesRegistry::fromPropertySchema($prop->getSchema());
            }
            self::$resourceFields[$name] = $fields;
        }

        return self::$resourceFields[$name];
    }

    /**
     * Retrieve a list of property names for a given object type $name
     *
     * @param string $name Object type name
     * @return array
     */
    public static function objectProperties($name)
    {
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($name);

        $properties = TableRegistry::getTableLocator()->get('Properties')->find('objectType', [$name])
            ->cache(sprintf('id_%s_props', $objectType->get('id')), self::CACHE_CONFIG)
            ->toArray();

        return $properties;
    }

    /**
     * Retrieve a list of property names for a given resource type $name
     *
     * @param string $name Resource type name
     * @return array
     */
    public static function resourceProperties($name)
    {
        $table = TableRegistry::getTableLocator()->get(Inflector::camelize($name));
        $entity = $table->newEmptyEntity();

        $properties = [];
        $names = array_diff($table->getSchema()->columns(), $entity->getHidden());
        foreach ($names as $name) {
            $properties[] = new StaticProperty(compact('name', 'table'));
        }

        return $properties;
    }

    /**
     * Retrieve a list of fields for input filter
     *
     * @param array $options Input filter options
     * @return array
     */
    public static function inputFilterFields($options = [])
    {
        if (empty(self::$filterFields)) {
            $fields = [];
            self::$filterFields = static::filterProperties($options);
        }

        return self::$filterFields;
    }

    /**
     * Retrieve input filter properties
     *
     * @param array $options Input filter options
     * @return array
     */
    public static function filterProperties($options = [])
    {
        return [
            'field_name' => [
                'type' => TypesRegistry::string(),
                'description' => 'Name of the field to filter',
            ],
            'field_value' => [
                'type' => TypesRegistry::string(),
                'description' => 'Field value to look up',
            ],
            'query' => [
                'type' => TypesRegistry::string(),
                'description' => 'Search query to perform',
            ],
        ];
    }
}
