# GraphQL plugin for BEdita4

A [GraphQL](http://graphql.org) plugin for BEdita4. Only ``Queries`` are supported for now, no ``Mutations``, with limited support.

## Installation

You can install this plugin in BEdita4 using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```bash
composer require bedita/graphql
```

## Setup

To activate the plugin just add this line to `Plugins` configuration (on `config/app.php`) or db

```php
 'BEdita/GraphQL' => ['autoload' => true, 'routes' => true],
```
