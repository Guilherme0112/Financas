<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
            'categoria_id' => 'required|exists:categorias,id',
            'mes_referencia' => 'nullable|date_format:d/m/Y',
        ];
    }

    protected function passedValidation()
    {
        if ($this->mes_referencia) {
            $this->merge([
                'mes_referencia' => Carbon::createFromFormat(
                    'd/m/Y',
                    $this->mes_referencia
                )->format('Y-m-d'),
            ]);
        }
    }
}
