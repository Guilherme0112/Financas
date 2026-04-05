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
        Schema::create('processamento_faturas_erros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assinatura_id')->constrained()->onDelete('cascade');

            $table->string('etapa');

            $table->text('mensagem_erro');
            $table->longText('stack_trace')->nullable();

            $table->json('payload')->nullable();

            $table->timestamp('resolvido_em')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processamento_faturas_erros');
    }
};
