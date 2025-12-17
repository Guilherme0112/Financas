<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::orderBy('nome')->get();
    }

    public function store(Request $request)
    {
        return Categoria::create(
            $request->validate([
                'nome' => 'required|string|max:255',
                'tipo' => 'required|in:ENTRADA,SAIDA',
            ])
        );
    }
}
