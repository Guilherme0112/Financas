<?php

namespace App\Http\Middleware;

use App\Enums\StatusAssinatura;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $assinatura = $user->assinatura;

        if (!$assinatura) {
            return redirect()->route('assinatura.expirada')->with('error', 'Você precisa de uma assinatura ativa para acessar esta área.');;
        }

        if ($assinatura->status === StatusAssinatura::PENDENTE || $assinatura->status === StatusAssinatura::EXPIRADA) {
            return redirect()->route('faturas.index')->with('error', 'Você precisa de uma assinatura ativa para acessar esta área.');
        }

        if ($assinatura->status !== StatusAssinatura::ATIVA) {
            return redirect()->route('assinatura.expirada')->with('error', 'Você precisa de uma assinatura ativa para acessar esta área.');
        }

        return $next($request);
    }
}
