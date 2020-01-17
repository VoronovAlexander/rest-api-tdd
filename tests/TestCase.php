<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertArraySubsetNew($array, $haystack) {
        foreach ($array as $key => $value) {
            $this->assertArrayHasKey($key, $haystack);
            $this->assertEquals($value, $haystack[$key]);
        }
    }
}
