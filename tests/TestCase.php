<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
        $this->seed();
    }

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
