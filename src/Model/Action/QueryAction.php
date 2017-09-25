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
use BEdita\GraphQL\Model\TypesRegistry;
use BEdita\GraphQL\Model\Type\QueryType;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

/**
 * Executes GraphQL query.
 *
 * @since 4.0.0
 */
class QueryAction extends BaseAction
{

    /**
     * GraphQL schema of current project
     *
     * @var \GraphQL\Schema
     */
    protected $schema;

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $data = array_merge(['query' => null, 'variables' => null, 'operationName' => null], $data);

        try {
            $this->buildSchema();

            $result = GraphQL::executeQuery(
                $this->schema,
                $data['query'],
                null,
                new AppContext(),
                $data['variables'],
                $data['operationName']
            );

            $result = $result->toArray();
        } catch (\Exception $e) {
            $result = [
                'errors' => [
                    'message' => $e->getMessage()
                ],
                'status' => 500,
            ];
        }

        return $result;
    }

    /**
     * Build GraphQL project schema
     *
     * @return void
     */
    protected function buildSchema()
    {
        $this->schema = new Schema([
            'query' => TypesRegistry::query()
        ]);
    }
}
