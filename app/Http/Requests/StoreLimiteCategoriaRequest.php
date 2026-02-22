<?php

namespace App\Http\Requests;

use App\Enums\CategoriaSaida;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLimiteCategoriaRequest extends FormRequest
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
            "categoria_saida" => [
                'required', 
                Rule::enum(CategoriaSaida::class)
            ],
            "limite" => 'required|decimal:2|min:1|max:999999.99',
            "notificar_ao_atingir" => 'required|boolean',
            "mes_referencia" => 'required|date_format:Y-m-d',
            'meses_recorrentes' => "nullable|numeric|min:1|max:12"
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'notificar_ao_atingir' => $this->boolean('notificar_ao_atingir'),
        ]);
    }
}
