<?php

namespace App\Http\Resources\Tokens;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
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
            'token_type' => $this->token_type,
            'expires_in' => $this->expires_in,
            'access_token'=> $this->access_token,
            'refresh_token'=> $this->refresh_token,
        ];
    }
}
