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
        Schema::table('defenses', function (Blueprint $table) {
             $table->enum('problema_motivo_contestacao', [
                'Erro na especificação técnica',
                'Erro no orçamento estimativo',
                'Violação Legal',
                'Prazo Inadequado',
                'Exigência Desproporcional',
            ])->default('Erro na especificação técnica')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->string('problema_motivo_contestacao')->nullable()->change();
        });
    }
};
