<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertArraySubsetNew($array, $haystack) {
        foreach ($array as $key => $value) {
            $this->assertArrayHasKey($key, $haystack);
            $this->assertEquals($value, $haystack[$key]);
        }
    }

    protected function responseValidationFailedTest(TestResponse $response) {
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
        $errors = $response->decodeResponseJson('errors');
        foreach ($errors as $property => $array) {
            $this->assertIsString($property);
            $this->assertIsArray($array);
            foreach ($array as $item) {
                $this->assertIsString($item);
            }
        }
    }
}
