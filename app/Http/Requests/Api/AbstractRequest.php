<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

abstract class AbstractRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['id'] = $this->route('item');

        return $data;
    }

}
