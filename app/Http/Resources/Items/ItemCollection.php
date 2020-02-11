<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Items\ItemResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'current_page' => (int) $this->resource->currentPage(),
            'total' => (int) $this->resource->total(),
            'per_page' => (int) $this->resource->perPage(),
        ];
    }
}
