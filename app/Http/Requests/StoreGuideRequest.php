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
        $guideId = $this->route('guide') ? $this->route('guide')->id : null;
        if (!$guideId && $this->route('slug')) {
            $guideId = \App\Models\Guide::where('slug', $this->route('slug'))->first()->id ?? null;
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
