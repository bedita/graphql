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

namespace BEdita\GraphQL\Model;

use BEdita\GraphQL\Model\Type\QueryType;
use BEdita\GraphQL\Model\Type\RolesType;
use BEdita\GraphQL\Model\Type\UsersType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * Class Types
 *
 * Acts as a registry and factory for your types.
 *
 * As simplistic as possible for the sake of clarity of this example.
 * Your own may be more dynamic (or even code-generated).
 *
 * @package BEdita\GraphQL\Model
 */
class Types
{
    private static $users;
    private static $roles;
    private static $query;

    /**
     * @return \BEdita\GraphQL\Model\Type\UsersType
     */
    public static function users()
    {
        return self::$users ?: (self::$users = new UsersType());
    }

    /**
     * @return \BEdita\GraphQL\Model\Type\RolesType
     */
    public static function roles()
    {
        return self::$roles ?: (self::$roles = new RolesType());
    }

    /**
     * @return \BEdita\GraphQL\Model\Type\QueryType
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }

    // Let's add internal types as well for consistent experience
    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
