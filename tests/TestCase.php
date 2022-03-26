<?php

namespace LaravelTickets\Tests;

use LaravelTickets\LaravelTickets;
use LaravelTickets\LaravelTicketsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan(
            'migrate',
            ['--database' => 'testbench']
        )->run();

        LaravelTickets::api();
        LaravelTickets::routes();
    }

    protected function getPackageProviders($app)
    {
        return [LaravelTicketsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
