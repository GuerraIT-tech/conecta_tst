<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('radar_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('id_compra', 60);
            $table->string('numero_controle_pncp', 80)->nullable();

            $table->string('orgao', 255)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('municipio', 120)->nullable();

            $table->string('modalidade', 120)->nullable();
            $table->timestamp('data_publicacao')->nullable();
            $table->timestamp('data_encerramento')->nullable();

            $table->text('objeto')->nullable();
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'id_compra']);
            $table->index(['user_id', 'data_publicacao']);
            $table->index(['user_id', 'uf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radar_results');
    }
};
