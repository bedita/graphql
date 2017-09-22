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

use BEdita\GraphQL\Model\AppContext;
use BEdita\GraphQL\Model\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class UsersType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Users',
            'description' => 'BE4 users',
            'fields' => function() {
                return [
                    'id' => Types::id(),
                    'email' => Types::string(),
                    'username' => Types::string(),
                    'name' => Types::string(),
                    'surname' => Types::string(),
                ];
            },
            // 'interfaces' => [
            //     Types::node()
            // ],
            'resolveField' => function($value, $args, $context, ResolveInfo $info) {
                $method = 'resolve' . ucfirst($info->fieldName);
                if (method_exists($this, $method)) {
                    return $this->{$method}($value, $args, $context, $info);
                } else {
                    return $value->{$info->fieldName};
                }
            }
        ];
        parent::__construct($config);
    }
}
