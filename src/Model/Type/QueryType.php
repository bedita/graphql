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

namespace BEdita\GraphQL\Model\Type;

use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Model\Action\ListObjectsAction;
use BEdita\GraphQL\Model\AppContext;
use BEdita\GraphQL\Model\TypesRegistry;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * GraphQL root BEdita query.
 *
 * @since 4.0.0
 */
class QueryType extends ObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => function () {
                return TypesRegistry::rootTypes();
            },
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->resolve($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    /**
     * Resolve a root type item in our resources/objects graph
     *
     * @param mixed $rootValue Root value - currently unused
     * @param mixed $args Arguments to resolve an item - currently only 'id' supported
     * @param AppContext $context Application context
     * @param ResolveInfo $info Resolve information
     *
     * @return mixed
     */
    public function resolve($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $type = TypesRegistry::inspectTypeName($info->fieldName);
        if ($type === false) {
            throw new BadRequestException(__d('bedita', 'Type name "{0}" not found', $info->fieldName));
        }

        $resolvers = [
            TypesRegistry::SINGLE_RESOURCE => 'resolveResource',
            TypesRegistry::RESOURCES_LIST => 'resolveResourcesList',
            TypesRegistry::SINGLE_OBJECT => 'resolveObject',
            TypesRegistry::OBJECTS_LIST => 'resolveObjectsList',
        ];
        $method = $resolvers[$type];

        return $this->{$method}($rootValue, $args, $context, $info);
    }

    /**
     * Resolve a single resource from its type name and id
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param AppContext $context Application context
     * @param ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveResource($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $singularized = array_flip(TypesRegistry::resourceTypeNames());
        $table = TableRegistry::get(Inflector::camelize($singularized[$info->fieldName]));
        $action = new GetEntityAction(compact('table'));

        return $action(['primaryKey' => $args['id']]);
    }

    /**
     * Resolve a resources list from a filter input
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param AppContext $context Application context
     * @param ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveResourcesList($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $table = TableRegistry::get(Inflector::camelize($info->fieldName));
        $action = new ListEntitiesAction(compact('table'));
        $filter = empty($args['filter']) ? [] : $args['filter'];

        return $action(compact('filter'));
    }

    /**
     * Resolve a single object from its type name and id
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param AppContext $context Application context
     * @param ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveObject($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $objectType = TableRegistry::get('ObjectTypes')->get($info->fieldName);
        $table = TableRegistry::get($objectType->alias);
        $action = new GetObjectAction(compact('table', 'objectType'));

        return $action(['primaryKey' => $args['id']]);
    }

    /**
     * Resolve an objects list from a filter input
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param AppContext $context Application context
     * @param ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveObjectsList($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $objectType = TableRegistry::get('ObjectTypes')->get($info->fieldName);
        $table = TableRegistry::get($objectType->alias);
        $action = new ListObjectsAction(compact('table', 'objectType'));
        $filter = empty($args['filter']) ? [] : $args['filter'];

        return $action(compact('filter'));
    }
}
