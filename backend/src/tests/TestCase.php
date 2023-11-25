<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Faker\Factory;
use Faker\Generator;

abstract class TestCase extends BaseTestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        // Artisan::call('migrate:refresh');
    }

    public function __get($key)
    {
        if ($key === 'faker') {
            return $this->faker;
        }
        throw new \Exception('Unknown Key Requested');
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
