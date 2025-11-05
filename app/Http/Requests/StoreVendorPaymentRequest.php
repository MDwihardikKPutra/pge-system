<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorPaymentRequest extends FormRequest
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
            'vendor_id' => 'required|exists:vendors,id',
            'project_id' => 'required|exists:projects,id',
            'payment_type' => 'required|string|in:project,kantor,lainnya',
            'payment_date' => 'required|date',
            'invoice_number' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
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
            'vendor_id.required' => 'Vendor harus dipilih.',
            'vendor_id.exists' => 'Vendor yang dipilih tidak valid.',
            'project_id.required' => 'Project harus dipilih.',
            'project_id.exists' => 'Project yang dipilih tidak valid.',
            'payment_type.required' => 'Tipe pembayaran harus dipilih.',
            'payment_type.in' => 'Tipe pembayaran harus berupa project, kantor, atau lainnya.',
            'payment_date.required' => 'Tanggal pembayaran harus diisi.',
            'invoice_number.required' => 'Nomor invoice harus diisi.',
            'amount.required' => 'Jumlah pembayaran harus diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran tidak boleh negatif.',
            'description.required' => 'Deskripsi harus diisi.',
        ];
    }
}
