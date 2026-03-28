<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Planos;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return Inertia::render('Auth/Register', [
            "planos" => Planos::options(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request, UserService $userService): Response
    {
        $dados = $request->validated();
        logger()->info("Requisição para criar usuário recebida e validada", $dados);

        $result = $userService->registrarComAssinatura($dados, $request->plano);

        if (isset($result['external']) && $result['external']) {
            return Inertia::location($result['redirect']);
        }

        if (isset($result['error'])) {
            return redirect()->to($result['redirect'])->with('error', $result['error']);
        }

        return redirect()->to($result['redirect']);
    }
}
