<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ItemUpdateRequest extends AbstractRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|exists:items,id',
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:3|max:5000',
            'is_important' => 'required|boolean'
        ];
    }
}
