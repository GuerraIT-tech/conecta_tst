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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('requesting_agency')->nullable()->comment('Nome do órgão solicitante');
            $table->enum('bidding_modality', [
                'Concorrência Eletrônica',
                'Pregão Eletrônico',
                'Pregão Eletrônico Registro de Preços',
                'Dispensa de Licitação'
            ])->comment('Modalidade da licitação');
            $table->string('bidding_number')->comment('Número da Licitação');
            $table->string('uasg_number')->nullable()->comment('Número da UASG (quando aplicável)');
            $table->timestamp('bidding_stage_start')->nullable()->comment('Início da etapa de lances');
            $table->timestamp('registration_deadline')->nullable()->comment('Término do credenciamento e inserção de preços');
            $table->string('platform_email')->nullable()->comment('Email da plataforma para etapa de lances (opcional)');
            $table->string('registration_email')->nullable()->comment('Email para credenciamento da empresa');
            $table->string('auctioneer_name')->nullable()->comment('Nome do pregoeiro');
            $table->string('auctioneer_email')->nullable()->comment('Email do pregoeiro');
            $table->string('auctioneer_phone')->nullable()->comment('Telefone do pregoeiro');
            $table->json('items')->nullable()->comment('Relação dos itens, descrição, quantidade, preço máximo');
            $table->enum('bidding_type', ['Aberta', 'Exclusiva (ME/EPP)', 'Mista'])
                ->default('Aberta')
                ->comment('Tipo de licitação: Open (Aberta), Exclusive (Exclusiva ME/EPP), Mixed (Mista)');
            $table->longText('required_documents')->nullable()->comment('Documentos solicitados no edital');
            $table->longText('required_declarations')->nullable()->comment('Declarações solicitadas no edital');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
