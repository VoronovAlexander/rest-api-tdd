<?php

namespace App\Http\Resources\Items;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this['id'],
            'title' => (string) $this['title'],
            'content' => (string) $this['content'],
            'is_important' => (bool) $this['is_important'],
            'created_at' => Carbon::parse($this['created_at'])->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this['updated_at'])->format('Y-m-d H:i:s'),
        ];
    }
}
