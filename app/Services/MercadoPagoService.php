<?php

namespace App\Services;

use App\Models\Fatura;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Resources\Payment;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    /**
     * Cria uma preferência de pagamento para uma Fatura específica
     */
    public function criarLinkPagamento(Fatura $fatura)
    {
        $client = new PreferenceClient();

        try {
            $items = [
                [
                    "id" => "FAT_" . ($fatura->id ?? 'NEW_' . $fatura->user_id . '_' . time()),
                    "title" => "Assinatura SaldoUp - " . ($fatura->assinatura->plano->nome ?? 'Plano Profissional'),
                    "description" => "Pagamento referente à fatura #" . ($fatura->id ?? 'Nova'),
                    "currency_id" => "BRL",
                    "quantity" => 1,
                    "unit_price" => (float) $fatura->valor
                ]
            ];

            $payer = [
                "name" => $fatura->user->name,
                "email" => $fatura->user->email,
            ];

            $request = [
                "items" => $items,
                "payer" => $payer,
                "external_reference" => (string) ($fatura->id ?? 'NEW_FATURA_' . time()),
                "back_urls" => [
                    "success" => route('dashboard'),
                    "failure" => route('dashboard'),
                    "pending" => route('dashboard'),
                ],
                "payment_methods" => [
                    "installments" => 1,
                ],
            ];

            $preference = $client->create($request);
            logger()->info('Preferência Mercado Pago criada', ['preference' => $preference]);
            return $preference;

        } catch (MPApiException $e) {
            $conteudoErro = $e->getApiResponse()->getContent();
            \Log::error("Erro ao criar preferência MP: " . json_encode($conteudoErro));
            throw $e;
        }
    }

    public function obterPreferenciaPorId(string $preferenceId): Payment
    {
        $client = new PaymentClient();
        try {
            return $client->get($preferenceId);
        } catch (MPApiException $e) {
            \Log::error("Erro ao buscar preferência MP {$preferenceId}: " . json_encode($e->getApiResponse()->getContent()));
            throw $e;
        }
    }
}