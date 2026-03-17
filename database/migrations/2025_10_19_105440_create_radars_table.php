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
        Schema::create('radars', function (Blueprint $table) {
            $table->id();
            $table->string('titulo'); // Título
            $table->enum('situacao', ['Novo', 'Urgente', 'Em Andamento', 'Concluído'])
                ->default('Novo');
            $table->enum('orgao', ['Prefeitura Municipal', 'Governo do Estado de Minas Gerais', 'Ministério Público'])
                ->default('Prefeitura Municipal');
            $table->decimal('valor', 15, 2)->nullable(); // Valor monetário
            $table->integer('relevancia')->default(0); // Campo numérico
            $table->dateTime('data_hora_encerramento')->nullable(); // Data e hora de encerramento
            $table->string('tempo_restante')->nullable(); // Texto com o tempo restante
            $table->text('descricao')->nullable(); // Texto longo
            $table->text('observacoes')->nullable(); // Texto longo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radars');
    }
};
