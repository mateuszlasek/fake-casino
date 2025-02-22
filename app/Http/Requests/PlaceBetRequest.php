<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'color'  => 'required|string|in:red,green,black',
            'amount' => 'required|numeric|min:1',
        ];
    }
}
