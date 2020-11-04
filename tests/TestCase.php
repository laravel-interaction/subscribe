<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Zing\LaravelSubscribe\SubscribeServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        config(
            [
                'database.default' => 'testing',
            ]
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SubscribeServiceProvider::class,
        ];
    }
}
