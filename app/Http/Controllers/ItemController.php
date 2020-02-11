<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\ItemDestroyRequest;
use App\Http\Requests\Api\ItemIndexRequest;
use App\Http\Requests\Api\ItemShowRequest;
use App\Http\Requests\Api\ItemStoreRequest;
use App\Http\Requests\Api\ItemUpdateRequest;
use App\Http\Resources\Items\ItemCollection;
use App\Http\Resources\Items\ItemResource;
use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function index(ItemIndexRequest $request)
    {
        $per_page = $request->get('per_page', 10);

        $items = Item::orderByDesc('id')
            ->paginate($per_page);

        return response(ItemCollection::make($items), 200);
    }

    public function store(ItemStoreRequest $request)
    {
        $data = $request->only('title', 'content', 'is_important');

        Item::create($data);

        return response(null, 201);
    }

    public function show(ItemShowRequest $request, $id)
    {
        $item = Item::find($id);
        return response(ItemResource::make($item), 200);
    }

    public function update(ItemUpdateRequest $request, $id)
    {
        $data = $request->only('title', 'content', 'is_important');

        $item = Item::find($id);
        $item->update($data);

        return response(null, 204);
    }

    public function destroy(ItemDestroyRequest $request, $id)
    {
        $item = Item::find($id);
        $item->delete();
        return response(null, 204);
    }
}
