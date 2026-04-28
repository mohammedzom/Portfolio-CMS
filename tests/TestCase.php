<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'x-api-key' => config('app.api_key', 'test-api-key'),
        ]);
    }

    protected function withoutApiKey(): static
    {
        unset($this->defaultHeaders['x-api-key']);

        return $this;
    }
}
