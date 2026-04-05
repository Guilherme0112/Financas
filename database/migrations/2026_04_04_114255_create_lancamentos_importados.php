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
        Schema::create('lancamentos_importados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->timestamp('quando_importou');
            $table->string('tipo')->nullable();
            $table->string('path')->nullable();
            $table->string('arquivo_nome')->nullable();
            $table->integer('total_linhas')->nullable();
            $table->text('erro_mensagem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos_importados');
    }
};
