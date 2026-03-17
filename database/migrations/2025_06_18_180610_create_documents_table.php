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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('arquivo')->nullable();

            $table->enum('tipo_documento', [
                'cartao_cnpj',
                'contrato_social_consolidacao',
                'rg_socio',
                'procuracao_representante',
                'certificado_simplificada_porte',
                'cnd_federal',
                'cnd_estadual',
                'cnd_municipal',
                'crf_fgts',
                'cndt_trabalhista',
                'declaracao_simples_nacional',
                'outros_certificados_fiscais',
                'balanco_patrimonial',
                'certidao_falencia_concordata',
                'declaracao_faturamento',
                'outros_doc_financeiros',
                'atestados_capacidade_tecnica',
                'art_responsabilidade_tecnica',
                'certificados_obra_servico',
                'iso',
                'cert_ambientais',
                'registro_federacao_conselho',
                'outras_certificacoes',
                'curriculo_profissional',
                'diplomas_certificados_curso',
                'registro_categoria_profissional',
                'outros_certificados_profissionais',
                'declaracao_inexistencia_fatos_impeditivos',
                'declaracao_cumprimento_art7',
                'declaracao_microempresa',
                'outras_declaracoes',
                'procuracao_conectar',
                'papel_timbrado',
                'documentos_adicionais',
            ])->nullable();

            $table->date('data_emissao')->nullable();
            $table->date('data_validade')->nullable();
            $table->string('orgao_emissor')->nullable();

            $table->text('observacoes')->nullable();

            $table->enum('status', ['valido', 'expirado', 'pendente_revisao', 'nao_conforme'])->default('pendente_revisao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
