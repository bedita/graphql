<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
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
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

/**
 * Executes GraphQL query.
 *
 * @since 5.0.0
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
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        $data = array_merge(['query' => null, 'variables' => null, 'operationName' => null], $data);

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

        return $result;
    }

    /**
     * Build GraphQL project schema
     *
     * @return void
     */
    protected function buildSchema(): void
    {
        $this->schema = new Schema([
            'query' => TypesRegistry::query(),
        ]);
    }
}
