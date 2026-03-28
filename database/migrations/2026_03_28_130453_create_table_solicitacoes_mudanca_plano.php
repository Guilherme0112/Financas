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
        Schema::create('solicitacoes_mudanca_plano', function (Blueprint $table) {
            $table->id();
            $table->foreignId("assinatura_id")->constrained("assinaturas")->onDelete("cascade");
            $table->foreignId("fatura_id")->constrained("faturas")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("plano_antigo_id")->constrained("planos")->onDelete("cascade");
            $table->foreignId("plano_novo_id")->constrained("planos")->onDelete("cascade");
            $table->enum("status", ["PENDENTE", "CONCLUIDO", "CANCELADO"])->default("PENDENTE");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_mudanca_plano');
    }
};
