<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreSchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"=> "required|unique:schools,name",
            'email' => 'required|email',
            'password' => [
                'required',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
            ]
        ];
    }

    public function messages() {
        return [
            'name.required' => 'El nombre de la institución es obligatorio.',
            'name.unique'   => 'El nombre de la institución ya se encuentra registrada.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo electrónico no tiene un formato válido.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos :min caracteres.',
            'password.letters'  => 'La contraseña debe contener letras.',
            'password.mixed'    => 'La contraseña debe tener mayúsculas y minúsculas.',
            'password.numbers'  => 'La contraseña debe incluir al menos un número.',
            'password.symbols'  => 'La contraseña debe incluir al menos un símbolo.',
            'password.uncompromised' => 'La contraseña es insegura por filtraciones previas. Por favor, cambiela.',
        ];
    }
}
