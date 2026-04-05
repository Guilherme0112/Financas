<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private AssinaturaService $assinaturaService,
        private PlanoService $planoService,
        private FaturaService $faturaService
    ) {
    }

    public function registrarComAssinatura(array $dados, ?string $planoId): array
    {
        return DB::transaction(function () use ($dados, $planoId) {
            $user = $this->userRepository->criarUsuario($dados);

            logger()->info("Usuário criado com sucesso", $user->toArray());

            $plano = $this->planoService->obterPlanoPorId($planoId);
            logger()->info("Plano encontrado com sucesso", $plano->toArray());

            $assinatura = $this->assinaturaService->prepararAssinaturaInicial($user, $plano);
            logger()->info("Assinatura criada com sucesso", $assinatura->toArray());

            $user->update(['assinatura_id' => $assinatura->id]);
            logger()->info("Assinatura vinculada ao usuário com sucesso");

            Auth::login($user);
            return $this->faturaService->processarFluxoFinanceiro($user, $assinatura, $plano);
        });
    }
}