<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'rating_cleanliness'   => 'required|integer|min:1|max:5',
            'rating_communication' => 'required|integer|min:1|max:5',
            'rating_location'      => 'required|integer|min:1|max:5',
            'rating_value'         => 'required|integer|min:1|max:5',
            'comment'              => 'required|string',
        ];
    }
}
