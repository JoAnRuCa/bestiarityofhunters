<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $guide = $this->route('guide');
        $guideId = null;

        if (is_object($guide)) {
            $guideId = $guide->id;
        } elseif (is_string($guide)) {
            $guideModel = \App\Models\Guide::where('slug', $guide)->first();
            $guideId = $guideModel ? $guideModel->id : null;
        }

        return [
            'titulo'    => ['required', 'string', 'max:255', 'unique:guides,titulo,' . $guideId],
            'contenido' => ['required', 'string'],
            'tags'      => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'    => 'Every scroll needs a title, Hunter.',
            'titulo.max'         => 'The title is too long for the archive.',
            'contenido.required' => 'A guide without knowledge is just an empty scroll.',
        ];
    }
}
