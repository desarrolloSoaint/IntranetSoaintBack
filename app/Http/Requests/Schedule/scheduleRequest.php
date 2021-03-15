<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class scheduleRequest extends FormRequest
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
            'start_time'    => 'required',
            'finish_time'   => 'required',
            'rule_id'       => 'required',
        ];
    }

    public function messages()
    {
        return [
            'start_time.required'   => 'La hora de inicio es requerida',
            'finish_time.required'  => 'La hora de fin es requerida',
            'rule_id.required'      => 'El tipo de horario es requerido'
        ];
    }
}