<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class registerRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

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
            'name'      => 'required',
            'password'  => 'required',
            'email'     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del permiso es requerido',
            'password.required' => 'La contraseña del permiso es requerido',
            'email.required' => 'El correo del permiso es requerido',
        ];
    }
}
