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
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * BEdita graphql input filter on objects list.
 *
 * @since 4.0.0
 */
class InputFilterType extends InputObjectType
{
    /**
     * {@inheritDoc}
     */
    public function __construct($options = [])
    {
        $config = [
            'name' => 'InputFilter',
            'description' => 'Input filter for objects list',
            'fields' => function () use ($options) {
                return FieldsRegistry::inputFilterFields($options);
            },
        ];
        parent::__construct($config);
    }
}
