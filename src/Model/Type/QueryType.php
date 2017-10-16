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
use BEdita\GraphQL\Model\AppContext;
use BEdita\GraphQL\Model\TypesRegistry;
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
        if (TypesRegistry::isAnObject($info->fieldName)) {
            $objectType = TableRegistry::get('ObjectTypes')->get($info->fieldName);
            $table = TableRegistry::get($objectType->alias);
            $action = new GetObjectAction(compact('table', 'objectType'));
        } else {
            $table = TableRegistry::get(Inflector::camelize($info->fieldName));
            $action = new GetEntityAction(compact('table'));
        }

        $data = $action(['primaryKey' => $args['id']]);

        return $data;
    }
}
