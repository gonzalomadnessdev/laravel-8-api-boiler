<?php

namespace App\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearUsuarioRequest extends FormRequest
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
            'nombre' => 'present|nullable|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|string|email:rfc|max:100|unique:users',
            'password' => 'required|string|min:6|max:100',
            'roles' => 'present|array|nullable',
        ];
    }
}
