<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditPlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::check())
        {
            return (Auth::user()->has_world_admin_access);
        }
        return false;
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
            'id'=>'required|exists:places,id',
            'name'=>'required|string',
            'image_url'=>'nullable|string',
            'summary'=>'nullable|string',
            'email_address'=>'nullable|email',
            'menu_link'=>'nullable|url',
            'website_url'=>'nullable|url',
            'eb_review_link'=>'nullable|url',
            'facebook_link'=>'nullable|string',
            'instagram_link'=>'nullable|string',
            'google_place_id'=>'nullable|string',
            'has_vegan_options'=>'nullable|boolean',
            'has_gluten_free_options'=>'nullable|boolean',
            'is_food_truck'=>'nullable|boolean',
            'serves_full_meals'=>'nullable|boolean',
            'serves_alcohol'=>'nullable|boolean',
            'has_public_wifi'=>'nullable|boolean',
            'has_bike_rack'=>'nullable|boolean',
            'has_carryout'=>'nullable|boolean',
            'serves_brunch'=>'nullable|boolean',
            'has_delivery'=>'nullable|boolean',
            'tags'=>'nullable|array',
            'tags.*.id'=>'nullable|exists:tags,id'
        ];
    }
}
