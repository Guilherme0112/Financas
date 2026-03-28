<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookMercadoPagoPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Regras baseadas no payload real do Mercado Pago.
     */
    public function rules(): array
    {
        return [
            'type'         => 'required|string',
            'action'       => 'required|string',
            'data'         => 'required|array',
            'data.id'      => 'required',  
            'user_id'      => 'required', 
            'api_version'  => 'nullable|string',
            'date_created' => 'nullable|string',
            'id'           => 'required',    
        ];
    }

    public function getPaymentId(): ?string
    {
        return $this->input('data.id');
    }

    public function isPaymentEvent(): bool
    {
        return $this->input('type') === 'payment';
    }
}