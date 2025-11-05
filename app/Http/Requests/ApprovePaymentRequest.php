<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePaymentRequest extends FormRequest
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
        // Check route name or method to determine if it's approve or reject
        $routeName = $this->route()->getName();
        $isReject = str_contains($routeName, 'reject');
        
        if ($isReject) {
            return [
                'rejection_reason' => 'required|string|max:500',
            ];
        }
        
        return [
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Alasan penolakan harus diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
