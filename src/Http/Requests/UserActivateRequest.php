<?php

namespace Omadonex\LaravelSupport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserActivateRequest extends FormRequest
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
            'token' => 'required',
            'username' => 'required|alpha_dash|max:25|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
