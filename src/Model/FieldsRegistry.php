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

use BEdita\GraphQL\Model\TypesRegistry;
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
     * Registry of resource fields
     *
     * @var array
     */
    private static $resourceFields = [];

    /**
     * Registry of object fields
     *
     * @var array
     */
    private static $objectFields = [];

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
            foreach($properties as $prop) {
                $fields[$prop] = TypesRegistry::string();
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
        $fields = [];
        $properties = static::resourceProperties($name);
        foreach($properties as $prop) {
            $fields[$prop] = TypesRegistry::string();
        }

        return $fields;
    }

    /**
     * Retrieve a list of property names for a given object type $name
     *
     * @param string $name Object type name
     * @return array
     */
    public static function objectProperties($name)
    {
        $objectType = TableRegistry::get('ObjectTypes')->get($name);
        $table =  TableRegistry::get($objectType->alias);

        $entity = $table->newEntity();
        return array_diff($table->getSchema()->columns(), $entity->hiddenProperties());
    }

    /**
     * Retrieve a list of property names for a given resource type $name
     *
     * @param string $name Resource type name
     * @return array
     */
    public static function resourceProperties($name)
    {
        $table = TableRegistry::get(Inflector::camelize($name));

        $entity = $table->newEntity();
        return array_diff($table->getSchema()->columns(), $entity->hiddenProperties());
    }
}
