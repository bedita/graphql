<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\GraphQL\Test\TestCase\Model\Type;

use BEdita\GraphQL\Model\Type\DateTimeType;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;

/**
 * {@see \BEdita\GraphQL\Model\Type\DateTimeType} Test Case
 *
 * @coversDefaultClass \BEdita\GraphQL\Model\Type\DateTimeType
 */
class DateTimeTypeTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\GraphQL\Model\Type\DateTimeType
     */
    public $dateTimeType;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dateTimeType = new DateTimeType();
    }

    /**
     * Data provider for `testSerialiaze`
     *
     * @return array
     */
    public function serialiazeProvider(): array
    {
        return [
            'time' => [
                FrozenTime::parse('2018-01-07 12:40:19'),
                '2018-01-07T12:40:19+00:00',
            ],
            'string' => [
                '2018-01-07T12:40:19+00:00',
                '2018-01-07T12:40:19+00:00',
            ],
            'bad' => [
                123456789,
                new InvariantViolation('DateTime can represent only strings or integer values: 123456789'),
            ],
        ];
    }

    /**
     * Test `serialize` method
     *
     * @return void
     * @covers ::serialize()
     * @dataProvider serialiazeProvider
     */
    public function testSerialiaze($input, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $result = $this->dateTimeType->serialize($input);
        static::assertEquals($result, $expected);
    }

    /**
     * Test `parseValue` method
     *
     * @return void
     * @covers ::parseLiteral()
     */
    public function testParseLiteral(): void
    {
        $date = '2018-01-01';
        $ast = new StringValueNode([]);
        $ast->value = $date;
        $result = $this->dateTimeType->parseLiteral($ast);
        static::assertEquals($result, $date);

        $result = $this->dateTimeType->parseLiteral(new StringValueNode([]));
        static::assertNull($result);
    }
}
