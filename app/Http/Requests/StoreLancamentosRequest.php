<?php

namespace App\Http\Requests;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
use App\Enums\TipoValor;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nome' => 'required|string|max:50',
            'descricao' => 'nullable|string|max:500',
            'valor' => 'required|decimal:2|min:1|max:999999.99',
            'recorrente' => 'required|boolean',
            'meses_recorrentes' => "nullable|numeric|min:1|max:12",
            'tipo' => [
                'required',
                Rule::enum(TipoValor::class)
            ],
            'categoria_entrada' => [
                "nullable",
                Rule::requiredIf($this->tipo === 'ENTRADA'),
                Rule::enum(CategoriaEntrada::class),
            ],

            'categoria_saida' => [
                "nullable",
                Rule::requiredIf($this->tipo === 'SAIDA'),
                Rule::enum(CategoriaSaida::class),
            ],
            'mes_referencia' => 'nullable|date_format:Y/m/d',
            'foi_pago' => 'nullable|boolean',
            "meta_id" => [
                "nullable",
                Rule::requiredIf($this->tipo === "RESERVA_META"),
                "exists:metas,id"
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'foi_pago' => $this->boolean('foi_pago'),
        ]);
    }
}
