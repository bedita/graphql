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
namespace BEdita\GraphQL\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\GraphQL\Controller\GraphQLController
 */
class GraphQLControllerTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    public $fixtures = [
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.streams',
        'plugin.BEdita/Core.media',
    ];

    /**
     * Data provider for `testContentType`
     *
     * @return array
     */
    public function contentTypeProvider(): array
    {
        return [
            'application json' => [
                 'application/json',
                 'POST',
                 '{"query": "query { user(id: \"1\") { username }}"}',
            ],
            // TODO: works from -  curl -H Content-Type:application/graphql http://be4.dev/graphql -d "query{users(id:\"1\"){username}}"
            // but not on on unit tests, why??
            // 'application graphql' => [
            //     'application/graphql',
            //     'POST',
            //     "query{users(id:\"1\"){username}}",
            // ],
            'no type' => [
                '',
                'POST',
                '{"query": "{ user(id: \"1\") { username }}"}',
            ],
            'simple get' => [
                '',
                'GET',
                '',
                'query={user(id:"1"){username}}',
            ],
        ];
    }

    /**
     * Test content type method.
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::readInput()
     * @dataProvider contentTypeProvider()
     */
    public function testContentType($contentType, $method, $body, $queryString = ''): void
    {
        $expected = [
            'data' => [
                'user' => [
                    'username' => 'first user',
                ],
            ],
        ];

        if ($method === 'GET') {
            $this->configRequestHeaders();
            $this->get('/graphql?' . $queryString);
        } else {
            $this->configRequestHeaders('POST', ['Content-Type' => $contentType]);
            $this->post('/graphql', $body);
        }
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testFailure`
     *
     * @return array
     */
    public function failureProvider(): array
    {
        return [
            'empty' => [
                 '{"query": "{}"}',
                 400,
            ],
            'wrong' => [
                '{"query": "{gustavo}"}',
                400,
            ],
        ];
    }

    /**
     * Test failure method.
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::readInput()
     * @dataProvider failureProvider()
     */
    public function testFailure($query, $status, $message = ''): void
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/graphql', $query);

        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode($status);
        $this->assertContentType('application/json');

        static::assertArrayHasKey('errors', $result);
        static::assertArrayNotHasKey('data', $result);
    }
}
