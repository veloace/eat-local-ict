<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProcessOwnershipClaimRequest extends FormRequest
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
            return Auth::user()->has_world_admin_access;
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
            'id'=>'required|exists:user_place_ownership_claims,id',
            'is_approved'=>'required|boolean',
            'is_rejected'=>'required|boolean',
            'admin_comments'=>'required'
        ];
    }
}
