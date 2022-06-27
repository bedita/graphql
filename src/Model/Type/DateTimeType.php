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

namespace BEdita\GraphQL\Model\Type;

use Cake\I18n\FrozenTime;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

/**
 * Internal DateTime Type
 */
class DateTimeType extends ScalarType
{
    /**
     * @var string
     */
    public $name = 'DateTime';

    /**
     * @var string
     */
    public $description = 'The `DateTime` scalar type represents a datetime, represented as ISO 8601 string';

    /**
     * Serialize input value to date time string

     * @param mixed $value Input data to serialize, must be Time or string
     * @return null|string
     */
    public function serialize($value)
    {
        if ($value instanceof FrozenTime) {
            return $value->jsonSerialize();
        }
        if (!is_string($value)) {
            throw new InvariantViolation(
                'DateTime can represent only strings or integer values: ' . Utils::printSafe($value)
            );
        }

        return (string)$value;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function parseValue($value)
    {
        return is_string($value) ? $value : null;
    }

    /**
     * @inheritDoc
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return $valueNode->value;
        }

        return null;
    }
}
