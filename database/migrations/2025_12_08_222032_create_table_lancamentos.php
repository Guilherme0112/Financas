<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lancamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('valor', 15, 2);

            $table->enum('tipo', ['ENTRADA', 'SAIDA']);

            $table->enum('categoria_entrada', [
                'SALARIO',
                'FREELANCE',
                'INVESTIMENTOS',
                'BONUS',
                'ALUGUEL',
                'REEMBOLSO',
                'VENDAS',
                'OUTROS',
            ])->nullable();

            $table->enum('categoria_saida', [
                'ALIMENTACAO',
                'MORADIA',
                'TRANSPORTE',
                'SAUDE',
                'EDUCACAO',
                'LAZER',
                'ASSINATURAS',
                'IMPOSTOS',
                'VESTUARIO',
                'OUTROS',
            ])->nullable();

            $table->boolean('recorrente')->default(false);
            $table->date('mes_referencia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos');
    }
};
