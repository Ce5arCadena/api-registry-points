<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class GetRegistryPointRequest extends FormRequest
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
        $authUser = Auth::user();
        return [
            "subject" => [
                "min:1",
                "integer", 
                "required",
                Rule::exists('subjects')
                    ->where('status', 'ACTIVE') 
                    ->where('school_id', $authUser->school_id)
            ]
        ];
    }
}
