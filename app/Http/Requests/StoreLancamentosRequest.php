<?php

namespace App\Http\Requests;

use App\Enums\CategoriaEntrada;
use App\Enums\CategoriaSaida;
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
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:1',
            'tipo' => 'required|in:ENTRADA,SAIDA',
            'recorrente' => 'required|boolean',
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
            'mes_referencia' => 'required|date_format:Y/m/d',
        ];
    }
}
