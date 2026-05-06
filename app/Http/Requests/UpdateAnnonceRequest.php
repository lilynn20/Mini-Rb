<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnonceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'titre'                => 'required|string|max:255',
            'description'          => 'required|string',
            'adresse'              => 'required|string|max:255',
            'ville'                => 'required|string|max:255',
            'prix_par_nuit'        => 'required|numeric|min:0',
            'nombre_de_chambres'   => 'required|integer|min:1',
            'images'               => 'nullable|array|max:10',
            'images.*'             => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_image_ids'    => 'nullable|array',
            'deleted_image_ids.*'  => 'integer',
        ];
    }
}
