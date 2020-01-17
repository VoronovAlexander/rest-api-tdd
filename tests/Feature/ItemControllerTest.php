<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testStore() {
        $item = factory(Item::class)->make();

        $response = $this->json('POST', 'api/items', $item->toArray());
        $response->assertStatus(201);

        $this->assertDatabaseHas('items', $item->toArray());
    }

    public function testIndex() {
        factory(Item::class, 100)->create();

        $response = $this->json('GET', 'api/items');

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
            ->limit(10)
            ->get()
            ->toArray();

        $receivedItems = $response->decodeResponseJson('data');

        $this->assertCount(10, $receivedItems);

        $this->assertEquals($receivedItems, $items);

    }

    public function testShow() {
        $item = factory(Item::class)->create();

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

        $response = $this->json('PUT', "api/items/$item->id", $newItem->toArray());

        $response->assertStatus(204);

        $item->refresh();

        $this->assertArraySubsetNew($newItem->toArray(), $item->toArray());
    }

    public function testDestroy() {
        $item = factory(Item::class)->create();

        $response = $this->json('DELETE', "api/items/$item->id");

        $response->assertStatus(204);

        $this->assertDeleted($item);
    }
}
