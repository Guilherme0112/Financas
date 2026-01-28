<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexLancamentosRequest extends FormRequest
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
            "page" => ["sometimes", "integer", "min:1", "max:100"],
            "tipo" => ["sometimes", "in:TODOS,ENTRADA,SAIDA"],
            "data_inicio" => ["sometimes", "date"],
            "data_fim" => ["sometimes", "date", "after_or_equal:data_inicio"],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('data_inicio') && !$this->has('data_fim')) {
            $this->merge([
                'data_inicio' => now()->startOfMonth()->toDateString(),
                'data_fim' => now()->endOfMonth()->toDateString(),
            ]);
        }
    }
}
