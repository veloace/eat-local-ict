<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MissingPlaceSuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'description'=>'required|min:10,max:175',
            'name'=>'nullable|max:50',
        ];
    }
}
