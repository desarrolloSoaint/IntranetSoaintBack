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
            'password' => [
                'required',
                'min:6',             // must be at least 6 characters in length
                // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                // 'regex:/[0-9]/',      // must contain at least one digit
                // 'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'email'     => 'required|unique:users|email',
            'role_id'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'La contraseña es requerido',
            'password.min' => 'La contraseña minimo 6 caracteres',
            'email.required' => 'El correo es requerido',
            'email.unique' => 'El correo ya se encuentra registrado',
            'email.email' => 'Ingrese un correo valido',
            'role_id.required' => 'El rol es requerido',
        ];
    }
}
