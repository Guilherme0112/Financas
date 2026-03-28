<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function criarUsuario(array $dados)
    {
        return User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'phone' => $dados['phone'],
            'password' => Hash::make($dados['password']),
        ]);
    }
}
