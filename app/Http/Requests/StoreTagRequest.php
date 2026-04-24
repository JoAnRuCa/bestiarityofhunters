<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $tagId = $this->route('tag') ? $this->route('tag')->id : null;

        return [
            'name' => ['required', 'string', 'max:50', 'unique:tags,name,' . $tagId],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A tag needs a name to categorize knowledge.',
            'name.unique'   => 'This tag already exists in the archive.',
            'name.max'      => 'The tag name is too long.',
        ];
    }
}
