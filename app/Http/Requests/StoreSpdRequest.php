<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpdRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'purpose' => 'required|string|max:1000',
            'cost_name' => 'required|array|min:1',
            'cost_name.*' => 'required|string|max:255',
            'cost_description' => 'nullable|array',
            'cost_description.*' => 'nullable|string|max:500',
            'cost_amount' => 'required|array|min:1',
            'cost_amount.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
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
            'project_id.required' => 'Project harus dipilih.',
            'project_id.exists' => 'Project yang dipilih tidak valid.',
            'destination.required' => 'Tujuan harus diisi.',
            'departure_date.required' => 'Tanggal keberangkatan harus diisi.',
            'return_date.required' => 'Tanggal kembali harus diisi.',
            'return_date.after_or_equal' => 'Tanggal kembali harus setelah atau sama dengan tanggal keberangkatan.',
            'purpose.required' => 'Tujuan harus diisi.',
            'cost_name.required' => 'Minimal satu biaya harus diisi.',
            'cost_name.*.required' => 'Nama biaya harus diisi.',
            'cost_amount.required' => 'Minimal satu biaya harus diisi.',
            'cost_amount.*.required' => 'Jumlah biaya harus diisi.',
            'cost_amount.*.numeric' => 'Jumlah biaya harus berupa angka.',
            'cost_amount.*.min' => 'Jumlah biaya tidak boleh negatif.',
            'documents.*.mimes' => 'Dokumen harus berupa file PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran dokumen maksimal 5MB.',
        ];
    }
}
