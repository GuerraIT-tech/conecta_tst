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
        Schema::create('lances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->before('id')->constrained('companies')->nullOnDelete();

            // 🔹 Primeira Aba: Estratégia
            $table->string('tipo_participacao')->nullable(); // Robô Automático, Manual, Híbrido
            $table->string('estrategia_lance')->nullable(); // Conservadora, Agressiva, Equilibrada
            $table->decimal('lance_maximo', 15, 2)->nullable(); // Valor monetário
            $table->integer('limite_tempo')->nullable(); // Minutos
            $table->decimal('incremento_padrao', 15, 2)->nullable(); // Valor monetário
            $table->decimal('margem_seguranca', 5, 2)->nullable(); // Percentual
            $table->boolean('incremento_automatico')->nullable(); // Sim/Não
            $table->boolean('notificacoes_tempo_real')->nullable(); // Sim/Não

            // 🔹 Segunda Aba: Proposta
            $table->string('licitacao_vencida')->nullable(); // Texto simples
            $table->decimal('lance_vencedor', 15, 2)->nullable(); // Valor monetário
            $table->integer('prazo_entrega')->nullable(); // Dias
            $table->text('condicoes_pagamento')->nullable(); // Texto longo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lances');
    }
};
