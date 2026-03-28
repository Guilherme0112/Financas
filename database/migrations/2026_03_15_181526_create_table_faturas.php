<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assinatura_id')->constrained()->onDelete('cascade');

            $table->decimal('valor', 10, 2);
            $table->enum('status', ['PENDENTE', 'PROCESSANDO', 'APROVADO', 'CANCELADO', 'REEMBOLSADO']);
            $table->enum('metodo_pagamento', ['PIX', 'BOLETO', 'CARTAO_CREDITO'])->nullable();
            
            $table->timestamp('vencimento_em')->nullable();
            $table->timestamp('periodo_inicio')->nullable();
            $table->timestamp('periodo_fim')->nullable();

            $table->string('referencia_externa')->nullable()->index();
            $table->string('url_pagamento')->nullable();
            $table->enum("tipo_cobranca", ['CICLO_NORMAL', 'UPGRADE', 'ESTORNO']);

            $table->timestamp('pago_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};
