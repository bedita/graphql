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

use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Plugin;
use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Utility\Security;

require_once 'vendor/autoload.php';

// Path constants to a few helpful things.
define('ROOT', dirname(__DIR__) . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . 'tests');
define('APP', ROOT . 'tests' . DS . 'test_app' . DS);
define('APP_DIR', 'test_app');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP);
define('LOGS', TMP);

$loader = new \Cake\Core\ClassLoader;
$loader->register();

require_once CORE_PATH . 'config/bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

if (!defined('API_KEY')) {
    define('API_KEY', 'API_KEY');
}

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite://127.0.0.1/' . TMP . 'graphql_test.sqlite');
}

ConnectionManager::drop('test');
ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::setConfig('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

FilesystemRegistry::dropAll();
Configure::write('Filesystem', [
    'default' => [
        'className' => 'BEdita/Core.Local',
        'path' => ROOT . 'vendor' . DS . 'bedita' . DS . 'core' . DS . 'tests' . DS . 'uploads',
        'baseUrl' => 'https://static.example.org/files',
    ],
]);

/* When debug = true the metadata cache should last
 * for a very very short time, as we want
 * to refresh the cache while developers are making changes.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._bedita_object_types_.duration', '+2 minutes');
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
}

/*
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set('UTC');

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    Configure::write('Log.debug.file', 'cli-debug');
    Configure::write('Log.error.file', 'cli-error');
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache') ?: []);
Email::setConfigTransport(Configure::consume('EmailTransport') ?: []);
Email::setConfig(Configure::consume('Email') ?: []);
Log::setConfig(Configure::consume('Log') ?: []);
Security::setSalt((string)Configure::consume('Security.salt'));
FilesystemRegistry::setConfig(Configure::consume('Filesystem') ?: []);

ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

Type::build('time')
    ->useImmutable()
    ->useLocaleParser();
Type::build('date')
    ->useImmutable()
    ->useLocaleParser();
Type::build('datetime')
    ->useImmutable()
    ->useLocaleParser();

Cache::drop('_bedita_object_types_');
Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);
Configure::write('debug', true);

$basePluginsPath = ROOT . 'vendor' . DS . 'bedita' . DS;

Plugin::load(
    'BEdita/Core',
    ['bootstrap' => true, 'path' => $basePluginsPath . 'core' . DS]
);

Plugin::load(
    'BEdita/API',
    ['bootstrap' => true, 'routes' => true, 'path' => $basePluginsPath . 'api' . DS]
);

Plugin::load(
    'BEdita/GraphQL',
    ['routes' => true, 'path' => ROOT]
);
