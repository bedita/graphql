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

namespace BEdita\GraphQL\Test\TestCase\Model\Type;

use BEdita\GraphQL\Model\Type\InputFilterType;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\GraphQL\Model\Type\InputFilterType} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Type\InputFilterType
 */
class InputFilterTypeTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * Test input filter type creation
     *
     * @return void
     *
     * @covers ::__construct()
     */
    public function testCreate()
    {
        $inputFilter = new InputFilterType();

        $fields = $inputFilter->getFields();

        $expected = [
            'field_name',
            'field_value',
            'query',
        ];

        static::assertNotEmpty($fields);
        foreach ($expected as $key) {
            static::assertArrayHasKey($key, $fields);
        }
    }
}
