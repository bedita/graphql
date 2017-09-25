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

use BEdita\GraphQL\Model\FieldsRegistry;
use GraphQL\Type\Definition\ObjectType as GraphQLObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class ResourcesType extends GraphQLObjectType
{
    public function __construct($options)
    {
        $config = [
            'name' => $options['name'],
            'description' => $options['name'] . ' type',
            'fields' => function() use ($options) {
                return FieldsRegistry::resourceFields($options['name']);
            },
            'resolveField' => function($value, $args, $context, ResolveInfo $info) {
                return $value->{$info->fieldName};
            }
        ];
        parent::__construct($config);
    }
}
