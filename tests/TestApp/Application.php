<?php
declare(strict_types=1);

namespace BEdita\GraphQL\Test\TestApp;

use BEdita\API\App\BaseApplication;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        $this->addPlugin('BEdita/Core', ['path' => dirname(dirname(__DIR__)) . DS . 'vendor/bedita/core/']);
        $this->addPlugin('BEdita/API', ['path' => dirname(dirname(__DIR__)) . DS . 'vendor/bedita/api/']);
        // Load GraphQL plugin
        $this->addPlugin('BEdita/GraphQL', ['path' => dirname(dirname(__DIR__)) . DS]);
    }
}
