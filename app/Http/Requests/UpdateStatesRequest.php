<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatesRequest extends FormRequest
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
        $userAuth = Auth::user();
        return [
            'ids' => "required|array|min:1",
            'ids.*' => "required|integer",
            'grade' => [
                "sometimes",
                "required",
                "numeric",
                Rule::exists('grades', 'id')->where(function (Builder $query) use ($userAuth) {
                    $query->where('school_id', $userAuth->school_id);  
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'   => 'Los ids son requeridos.',
            'ids.array'      => 'Debe ser un array con los ids.',
            'ids.min'        => 'Debe enviar al menos un id.',
            'ids.*.required' => 'Cada id es requerido.',
            'ids.*.integer'  => 'Cada id debe ser un número entero.',
            'grade.required' => "El curso es obligatorio",
            'grade.numeric'  => "El curso debe ser un número.",
            'grade.exists'   => "El curso especificado no existe."    
        ];
    }
}
