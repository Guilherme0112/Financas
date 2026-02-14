<?php

namespace App\Http\Requests;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExportarXLSXRequest extends FormRequest
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
            "page" => ["nullable", "integer", "min:1", "max:100"],
            "tipo" => ["nullable", "in:TODOS,ENTRADA,SAIDA"],
            "tipo_arquivo" => ["nullable", "in:csv,xlsx"],
            "data_inicio" => ["nullable", "date"],
            "data_fim" => ["nullable", "date", "after_or_equal:data_inicio"],
            'foi_pago' => ['nullable', 'boolean'],
            "recorrentes" => ['nullable', 'boolean'],
            "categoria_entrada" => [
                "nullable",
                Rule::enum(CategoriaEntrada::class)
            ],
            "categoria_saida" => [
                "nullable",
                Rule::enum(CategoriaSaida::class)
            ]
        ];
    }
}
