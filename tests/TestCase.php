<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withHeaders([
            'x-api-key' => env('API_KEY', 'test-api-key'),
        ]);
    }
}
