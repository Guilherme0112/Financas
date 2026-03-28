<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user()?->load('assinatura.plano');
        $trialInfo = null;

        if ($user && $user->assinatura && $user->assinatura->plano) {
            $assinatura = $user->assinatura;
            $plano = $assinatura->plano;

            if ($plano->plano->value === \App\Enums\Planos::GRATUITO->value) {
                $hoje = now();
                $fim = $assinatura->data_fim;
                $diasRestantes = $hoje->diffInDays($fim, false);

                $trialInfo = [
                    'is_trial' => true,
                    'days_remaining' => max(0, (int) $diasRestantes),
                ];
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'trial_info' => $trialInfo,
        ];
    }
}
