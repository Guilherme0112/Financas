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
        Schema::create('limite_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('categoria_saida');
            $table->decimal('limite', 10, 2);
            $table->string('mes_referencia', 7);
            $table->boolean('notificar_ao_atingir')->default(false);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->index('user_id');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limite_categorias');
    }
};
