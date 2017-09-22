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

namespace BEdita\GraphQL\Model\Action;

use BEdita\Core\Model\Action\BaseAction;
use BEdita\GraphQL\Model\AppContext;
use BEdita\GraphQL\Model\Types;
use GraphQL\GraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Executes GraphQL query.
 *
 * @since 4.0.0
 */
class QueryAction extends BaseAction
{
    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $data = array_merge(['query' => null, 'variables' => null, 'operationName' => null], $data);

        $schema = new Schema([
            'query' => Types::query()
        ]);

        $appContext = new AppContext();

        try {
            $result = GraphQL::execute(
                $schema,
                $data['query'],
                null,
                $appContext,
                $data['variables'],
                $data['operationName']
            );

        } catch (\Exception $e) {
            $result = [
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        return $result;
    }
}
