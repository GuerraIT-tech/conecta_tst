<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saved_pregoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('id_compra', 32);
            $table->string('numero_controle_pncp', 64)->nullable()->index();

            $table->string('orgao', 255)->nullable()->index();
            $table->string('uf', 2)->nullable()->index();
            $table->string('municipio', 120)->nullable()->index();

            $table->string('modalidade', 120)->nullable()->index();
            $table->string('modo_disputa', 120)->nullable();

            $table->string('processo', 80)->nullable()->index();
            $table->boolean('srp')->default(false)->index();

            $table->decimal('valor_estimado', 18, 2)->nullable()->index();

            $table->dateTime('data_publicacao')->nullable()->index();
            $table->dateTime('data_abertura')->nullable();
            $table->dateTime('data_encerramento')->nullable()->index();

            $table->text('objeto')->nullable();

            // Opcional: snapshot do retorno do PNCP (não é obrigatório)
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'id_compra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_pregoes');
    }
};
