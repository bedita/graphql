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

use BEdita\API\Error\ExceptionRenderer;
use BEdita\GraphQL\Test\TestApp\Application;
use Cake\Cache\Cache;
use Cake\Cache\Engine\ArrayEngine;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ConsoleErrorHandler;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new \Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

$_SERVER['PHP_SELF'] = '/';

require_once 'vendor/autoload.php';

define('ROOT', $root . DS . 'tests' . DS);
define('APP', ROOT . 'TestApp' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('LOGS', ROOT . DS . 'logs' . DS);
define('CONFIG', APP . DS . 'config' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CORE_PATH', $root . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('RESOURCES', ROOT . DS . 'resources' . DS);
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'BEdita\GraphQL\Test\TestApp',
    'encoding' => 'UTF-8',
    'defaultLocale' => 'en_US',
    'paths' => [
        'plugins' => [ROOT . DS . 'plugins' . DS],
        'templates' => [ROOT . DS . 'templates' . DS],
        'locales' => [RESOURCES . 'locales' . DS],
    ],
]);

Log::setConfig([
    'debug' => [
        'engine' => ConsoleLog::class,
        'levels' => ['notice', 'info', 'debug'],
    ],
    'error' => [
        'engine' => ConsoleLog::class,
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
    ],
]);

Cache::drop('_bedita_object_types_');
Cache::drop('_bedita_core_');
Cache::setConfig([
    '_cake_core_' => ['engine' => ArrayEngine::class],
    '_cake_model_' => ['engine' => ArrayEngine::class],
    '_cake_routes_' => ['engine' => ArrayEngine::class],
    '_bedita_object_types_' => ['engine' => ArrayEngine::class],
    '_bedita_core_' => ['engine' => ArrayEngine::class],
]);

Configure::write('Error', [
    'errorLevel' => E_ALL,
    'exceptionRenderer' => ExceptionRenderer::class,
    'skipLog' => [],
    'log' => true,
    'trace' => true,
    'ignoredDeprecationPaths' => ['cakephp/cakephp/src/TestSuite/Fixture/FixtureInjector.php'],
]);

Configure::write('Plugins', []);
Configure::write('Datasources', [
    'default' => [
        'url' => env('DATABASE_URL', 'sqlite:///tmp/bedita5.sqlite'),
    ],
    'test' => [
        'url' => env('DATABASE_TEST_URL', 'sqlite:///tmp/bedita5_test.sqlite'),
    ],
]);

ConnectionManager::setConfig(Configure::consume('Datasources') ?: []);
if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);
}
ConnectionManager::alias('test', 'default');

$app = new Application(APP . '/config');
$app->bootstrap();
$app->pluginBootstrap();

Router::reload();
Security::setSalt('BEDITA_SUPER_SECURE_RANDOM_STRING');

// clear all before running tests
TableRegistry::getTableLocator()->clear();
Cache::clear('_cake_core_');
Cache::clear('_cake_model_');
Cache::clear('_cake_routes_');
Cache::clear('_bedita_object_types_');
Cache::clear('_bedita_core_');

if (!defined('API_KEY')) {
    define('API_KEY', 'API_KEY');
}

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::setConfig('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

$now = FrozenTime::parse('2018-01-01T00:00:00Z');
FrozenTime::setTestNow($now);
FrozenDate::setTestNow($now);

date_default_timezone_set(env('BEDITA_DEFAULT_TIMEZONE', 'UTC'));
mb_internal_encoding(Configure::read('App.encoding'));
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

(new ConsoleErrorHandler(Configure::read('Error')))->register();
