<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLancamentosRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:ENTRADA,SAIDA',
            'recorrente' => 'required|boolean',
            'mes_referencia' => 'nullable|date_format:d/m/Y',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'valor' => floatval($this->valor),
        ]);
    }
}
