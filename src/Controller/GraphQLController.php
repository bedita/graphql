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
namespace BEdita\GraphQL\Controller;

use BEdita\API\Controller\AppController as BaseController;
use BEdita\GraphQL\Model\Action\QueryAction;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Utility\Hash;

/**
 * GraphQL controller endpoint
 *
 * See http://graphql.org
 */
class GraphQLController extends BaseController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);

        $contentType = $this->request->contentType();
        if ($this->request->is('post') && $contentType !== 'application/graphql') {
            if ($contentType !== 'application/json') {
                $this->request = $this->request->withHeader('Content-Type', 'application/json');
            }
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }

        $this->viewBuilder()->className('Json');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(Event $event)
    {
    }

    /**
     * Parse input and return formatted array
     * see http://graphql.org/learn/serving-over-http/#http-methods-headers-and-body
     *
     * @return array
     */
    protected function readInput()
    {
        $data = $this->request->getData();
        if (!empty($data)) {
            return $data;
        }

        if ($this->request->is('get')) {
            $data = [
                'query' => $this->request->getQuery('query'),
                'operationName' => $this->request->getQuery('operationName'),
                'variables' => $this->request->getQuery('variables'),
            ];
        } elseif ($this->request->contentType() === 'application/graphql') {
            $data = ['query' => file_get_contents('php://input')];
        }

        return $data;
    }

    /**
     * Executes a GraphQL query
     *
     * @return void
     */
    public function execute()
    {
        $this->request->allowMethod(['get', 'post']);

        $data = $this->readInput();
        $action = new QueryAction();
        $result = $action($data);

        $key = !empty($result['data']) ? 'data' : 'errors';

        if ($key === 'errors') {
            $status = Hash::get($result, 'status', 400);
            $this->response = $this->response->withStatus($status);
        }
        $this->set([$key => $result[$key]]);
        $this->set('_serialize', [$key]);
    }
}
