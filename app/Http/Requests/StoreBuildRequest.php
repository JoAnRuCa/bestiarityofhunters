<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Decodificamos los JSON para que Laravel pueda validar sus claves internas
        $this->merge([
            'build_data_array' => json_decode($this->build_data, true),
        ]);
    }

    public function rules()
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'playstyle' => ['nullable', 'string', 'max:2000'],
            
            // Validamos piezas específicas dentro del objeto decodificado
            'build_data_array.weapon1' => ['required'], 
            'build_data_array.head'    => ['required'],
            'build_data_array.chest'   => ['required'],
            'build_data_array.arms'    => ['required'],
            'build_data_array.waist'   => ['required'],
            'build_data_array.legs'    => ['required'],
            'build_data_array.charm' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The build needs a name, Hunter.',
            // Mensajes para las piezas
            'build_data_array.weapon1.required' => 'A Hunter is nothing without a weapon.',
            'build_data_array.head.required'    => 'You forgot your helmet.',
            'build_data_array.chest.required'   => 'Equip some chest armor.',
            'build_data_array.arms.required'    => 'Equip some arm guards.',
            'build_data_array.waist.required'   => 'Equip a waist piece.',
            'build_data_array.legs.required'    => 'Equip some greaves.',
            'build_data_array.charm.required' => 'A hunter needs a charm for that extra edge.',
        ];
    }
}