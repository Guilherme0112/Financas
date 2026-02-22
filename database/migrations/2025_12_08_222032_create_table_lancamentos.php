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
            $table->enum('tipo', ["ENTRADA", "SAIDA", "RESERVA_META", "RESERVA_EMERGENCIA"]);
            $table->string('categoria_entrada')->nullable();
            $table->string('categoria_saida')->nullable();
            $table->boolean(column: 'foi_pago')->nullable()->default(false);
            $table->boolean('recorrente')->default(false);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('meta_id')->nullable()->constrained('metas')->nullOnDelete();
            $table->date('mes_referencia')->nullable();

            $table->index('user_id');
            $table->index('meta_id');
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
