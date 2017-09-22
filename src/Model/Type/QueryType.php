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
use BEdita\GraphQL\Model\Types;
use Cake\ORM\TableRegistry;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'users' => [
                    'type' => Types::users(),
                    'description' => 'Returns user by id',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ]
                ],
                'roles' => [
                    'type' => Types::roles(),
                    'description' => 'Returns role by id',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ]
                ],
            ],
            'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    public function users($rootValue, $args, AppContext $context)
    {
        $action = new GetObjectAction(['table' => TableRegistry::get('Users')]);
        $data = $action(['primaryKey' => $args['id']]);

        return $data;
    }

    public function roles($rootValue, $args, AppContext $context)
    {
        $action = new GetEntityAction(['table' => TableRegistry::get('Roles')]);
        $data = $action(['primaryKey' => $args['id']]);

        return $data;
    }
}
