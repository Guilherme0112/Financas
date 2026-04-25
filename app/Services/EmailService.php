<?php

namespace App\Services;

use App\Mail\BemVindoEmail;
use App\Mail\UpgradePlanoEmail;
use App\Models\Fatura;
use App\Models\User;
use Mail;

class EmailService
{
    public function sendEmailBoasVindas(User $user)
    {
        try {
            Mail::to($user->email)->queue(new BemVindoEmail($user));
        } catch (\Exception $e) {
            logger()->error("Erro ao enviar e-mail de boas-vindas para {$user->email}: ".$e->getMessage());
        }
    }

    public function sendUpgradeSuccessEmail(User $user, Fatura $fatura): void
    {
        try {
            Mail::to($user->email)->queue(new UpgradePlanoEmail($user, $fatura));
        } catch (\Exception $e) {
            logger()->error('Erro ao enviar e-mail de upgrade: '.$e->getMessage());
        }
    }
}
