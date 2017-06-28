<?php

namespace Larrock\ComponentUsers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
			'email' => 'required|email',
			'fio' => 'required',
			'password' => 'required_with:old-password',
			'tel' => 'required',
			'address' => 'required',
		];
    }
}
