<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    private function errorCasesForStoreOrUpdate() :array
    {
        return [
            [
                // title => required
                'content' => $this->faker->realText(200),
                'is_important' => $this->faker->boolean()
            ],
            [
                // title => min:3
                'title' => $this->faker->lexify('??'),
                'content' => $this->faker->realText(200),
                'is_important' => $this->faker->boolean()
            ],
            [
                // title => max:255
                'title' => $this->faker->regexify('[A-Za-z0-9 ]{256}'),
                'content' => $this->faker->realText(200),
                'is_important' => $this->faker->boolean()
            ],
            [
                // content => required
                'title' => $this->faker->realText(255),
                'is_important' => $this->faker->boolean()
            ],
            [
                // content => min:2
                'title' => $this->faker->realText(255),
                'content' => $this->faker->lexify('??'),
                'is_important' => $this->faker->boolean()
            ],
            [
                // content => max:5000
                'title' => $this->faker->realText(255),
                'content' => $this->faker->regexify('[A-Za-z0-9 ]{5001}'),
                'is_important' => $this->faker->boolean()
            ],
            [
                // is_important => required
                'title' => $this->faker->realText(255),
                'content' => $this->faker->realText(5000),
            ],
            [
                // is_important => boolean
                'title' => $this->faker->realText(255),
                'content' => $this->faker->realText(5000),
                'is_important' => $this->faker->lexify('?'),
            ],
            [
                // is_important => boolean
                'title' => $this->faker->realText(255),
                'content' => $this->faker->realText(5000),
                'is_important' => $this->faker->numberBetween(2,9)
            ]
        ];
    }

    public function testStore() {
        $item = factory(Item::class)->make();

        $errorCases = $this->errorCasesForStoreOrUpdate();

        foreach ($errorCases as $errorCase) {
            $response = $this->json('POST', 'api/items', $errorCase);
            $this->responseValidationFailedTest($response);
        }

        $response = $this->json('POST', 'api/items', $item->toArray());
        $response->assertStatus(201);

        $this->assertDatabaseHas('items', $item->toArray());
    }

    public function testIndex() {
        factory(Item::class, 100)->create();

        $errorCases = [
            [],
            [
                'page' => 0,
            ],
            [
                'page' => 1,
                'per_page' => 0,
            ],
            [
                'page' => 1,
                'per_page' => 30
            ]
        ];

        foreach ($errorCases as $errorCase) {
            $response = $this->json('GET', 'api/items', $errorCase);
            $this->responseValidationFailedTest($response);
        }

        $per_page = $this->faker->numberBetween(1, 25);

        $response = $this->json('GET', 'api/items', [
            'page' => 1,
            'per_page' => $per_page
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'is_important',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'total',
                'per_page',
                'current_page'
            ]);

        $items = Item::orderByDesc('id')
            ->limit($per_page)
            ->get()
            ->toArray();

        $receivedItems = $response->decodeResponseJson('data');

        $this->assertCount($per_page, $receivedItems);

        $this->assertEquals($receivedItems, $items);

    }

    public function testShow() {
        $item = factory(Item::class)->create();

        $errorCases = [
            0,
            -1,
            100500,
        ];

        foreach ($errorCases as $errorCase) {
            $response = $this->json('GET', "api/items/$errorCase");
            $this->responseValidationFailedTest($response);
        }

        $response = $this->json('GET', "api/items/$item->id");

        $response->assertStatus(200)
            ->assertJsonStructure([
               'id',
               'title',
               'content',
               'is_important',
               'created_at',
               'updated_at'
            ]);

        $receivedItem = $response->decodeResponseJson();

        $this->assertEquals($item->toArray(), $receivedItem);
    }

    public function testUpdate() {
        $item = factory(Item::class)->create();
        $newItem = factory(Item::class)->make();

        $errorCases = $this->errorCasesForStoreOrUpdate();

        foreach ($errorCases as $errorCase) {
            $response = $this->json('PUT', "api/items/$item->id", $errorCase);
            $this->responseValidationFailedTest($response);
        }

        $response = $this->json('PUT', "api/items/$item->id", $newItem->toArray());

        $response->assertStatus(204);

        $item->refresh();

        $this->assertArraySubsetNew($newItem->toArray(), $item->toArray());
    }

    public function testDestroy() {
        $item = factory(Item::class)->create();

        $errorCases = [
            0,
            -1,
            100500,
        ];

        foreach ($errorCases as $errorCase) {
            $response = $this->json('DELETE', "api/items/$errorCase");
            $this->responseValidationFailedTest($response);
        }

        $response = $this->json('DELETE', "api/items/$item->id");

        $response->assertStatus(204);

        $this->assertDeleted($item);
    }
}
