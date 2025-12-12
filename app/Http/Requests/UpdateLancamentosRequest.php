<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLancamentosRequest extends FormRequest
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
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string',
            'valor' => 'sometimes|required|numeric|min:0',
            'tipo' => 'sometimes|required|in:ENTRADA,SAIDA',
            'recorrente' => 'sometimes|required|boolean',
            'mes_referencia' => 'sometimes|nullable|date_format:d/m/Y',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('valor')) {
            $this->merge([
                'valor' => floatval($this->valor),
            ]);
        }
    }
}
