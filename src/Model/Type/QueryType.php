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

/**
 * GraphQL root BEdita query.
 *
 * @since 4.0.0
 */
class QueryType extends ObjectType
{
    /**
     * @inheritDoc
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
            },
        ];
        parent::__construct($config);
    }

    /**
     * Resolve a root type item in our resources/objects graph
     *
     * @param mixed $rootValue Root value - currently unused
     * @param mixed $args Arguments to resolve an item - currently only 'id' supported
     * @param \BEdita\GraphQL\Model\AppContext $context Application context
     * @param \GraphQL\Type\Definition\ResolveInfo $info Resolve information
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
     * @param \BEdita\GraphQL\Model\AppContext $context Application context
     * @param \GraphQL\Type\Definition\ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveResource($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $singularized = array_flip(TypesRegistry::resourceTypeNames());
        $table = TableRegistry::getTableLocator()->get(Inflector::camelize($singularized[$info->fieldName]));
        $action = new GetEntityAction(compact('table'));

        return $action(['primaryKey' => $args['id']]);
    }

    /**
     * Resolve a resources list from a filter input
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param \BEdita\GraphQL\Model\AppContext $context Application context
     * @param \GraphQL\Type\Definition\ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveResourcesList($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $table = TableRegistry::getTableLocator()->get(Inflector::camelize($info->fieldName));
        $action = new ListEntitiesAction(compact('table'));
        $filter = $this->createFilter($args);

        return $action(compact('filter'));
    }

    /**
     * Create `filter` array from query args
     *
     * @param mixed $args Arguments to resolve items
     * @return array Filter array
     */
    protected function createFilter($args)
    {
        if (empty($args['filter'])) {
            return [];
        }
        $filter = $args['filter'];
        if (!empty($filter['field_name'])) {
            $filter[$filter['field_name']] = empty($filter['field_value']) ? null : $filter['field_value'];
            unset($filter['field_name'], $filter['field_value']);
        }

        return $filter;
    }

    /**
     * Resolve a single object from its type name and id
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param \BEdita\GraphQL\Model\AppContext $context Application context
     * @param \GraphQL\Type\Definition\ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveObject($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($info->fieldName);
        $table = TableRegistry::getTableLocator()->get($objectType->alias);
        $action = new GetObjectAction(compact('table', 'objectType'));

        return $action(['primaryKey' => $args['id']]);
    }

    /**
     * Resolve an objects list from a filter input
     *
     * @param mixed $rootValue Root value
     * @param mixed $args Arguments to resolve items
     * @param \BEdita\GraphQL\Model\AppContext $context Application context
     * @param \GraphQL\Type\Definition\ResolveInfo $info Resolve information
     * @return mixed
     */
    protected function resolveObjectsList($rootValue, $args, AppContext $context, ResolveInfo $info)
    {
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($info->fieldName);
        $table = TableRegistry::getTableLocator()->get($objectType->alias);
        $action = new ListObjectsAction(compact('table', 'objectType'));
        $filter = $this->createFilter($args);

        return $action(compact('filter'));
    }
}
