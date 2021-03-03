<?php

namespace App\Http\Requests\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ruleRequest extends FormRequest
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
            'name'          => 'required|unique:rules',
            'description'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la regla es requerido',
            'name.unique' => 'La regla ya se encuentra registrada',
            'description.required' => 'La descripcion de la regla es requerida',
        ];
    }
}
