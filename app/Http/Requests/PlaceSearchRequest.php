<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceSearchRequest extends FormRequest
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
            'is_open'=>'nullable|boolean',
            'name'=>'nullable|string',
            'lat'=>'nullable',
            'lng'=>'nullable',
            'distance'=>'nullable',
            'vegan'=>'nullable|boolean',
            'glutenFree'=>'nullable|boolean',
            'alcohol'=>'nullable|boolean',
            'wifi'=>'nullable|boolean',
            'bikeRack'=>'nullable|boolean',
            'meals'=>'nullable|boolean',
            'favorited'=>'nullable|boolean',
            'saved'=>'nullable|boolean',
            'delivery'=>'nullable|boolean',
            'brunch'=>'nullable|boolean',
            'charger'=>'nullable|boolean',
            'parking'=>'nullable|boolean',
            'tags'=>'nullable|array'
        ];
    }
}
