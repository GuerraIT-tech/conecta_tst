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
        Schema::create('defenses', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_contestacao', [
                'Inpugnação',
                'Recurso',
                'Representação'
            ]);
            $table->string('numero_processo')->nullable();
            $table->string('problema_motivo_contestacao')->nullable();
            $table->string('documento')->nullable();
            $table->timestamp('prazo')->nullable();
            $table->enum('orgao', ['Prefeitura Municipal', 'Governo do Estado', 'Autarquia Federal'])
                ->default('Prefeitura Municipal');
            $table->string('arquivo')->nullable();
            $table->enum('status', ['Em Elaboração', 'Em Análise', 'Aguardando Decisão', 'Decidida', 'Cancelada', 'Concluida'])
                ->default('Em Elaboração');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defenses');
    }
};
