<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
            'type' => 'required|string|in:barang,jasa',
            'category' => 'required|string|in:project,kantor,lainnya',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
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
            'type.required' => 'Tipe pembelian harus dipilih.',
            'type.in' => 'Tipe pembelian harus berupa barang atau jasa.',
            'category.required' => 'Kategori harus dipilih.',
            'category.in' => 'Kategori harus berupa project, kantor, atau lainnya.',
            'item_name.required' => 'Nama item harus diisi.',
            'description.required' => 'Deskripsi harus diisi.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.integer' => 'Jumlah harus berupa bilangan bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'unit.required' => 'Satuan harus diisi.',
            'unit_price.required' => 'Harga satuan harus diisi.',
            'unit_price.numeric' => 'Harga satuan harus berupa angka.',
            'unit_price.min' => 'Harga satuan tidak boleh negatif.',
            'documents.*.mimes' => 'Dokumen harus berupa file PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran dokumen maksimal 5MB.',
        ];
    }
}
