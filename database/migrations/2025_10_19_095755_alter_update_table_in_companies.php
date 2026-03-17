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
        Schema::table('companies', function (Blueprint $table) {
            // Campos pessoais
            $table->string('rg')->nullable()->after('corporate_name');
            $table->string('cpf')->nullable()->after('rg');

            // Campos da empresa
            $table->string('company_size')->nullable()->after('cnpj'); // Porte da Empresa
            $table->string('company_activities')->nullable()->after('company_size'); // Atividades da Empresa
            $table->date('opening_date')->nullable()->after('company_activities'); // Data de Abertura
            $table->decimal('share_capital', 15, 2)->nullable()->after('opening_date'); // Capital Social

            // Contatos adicionais
            $table->string('mobile_phone')->nullable()->after('phone'); // Telefone Celular
            $table->string('secondary_email')->nullable()->after('email'); // Email Secundário

            // Plataformas governamentais
            $table->boolean('comprasnet')->default(false)->after('website');
            $table->boolean('bec')->default(false)->after('comprasnet');
            $table->boolean('pregao_eletronico')->default(false)->after('bec');
            $table->boolean('sicaf')->default(false)->after('pregao_eletronico');
            $table->boolean('pncp')->default(false)->after('sicaf'); // Portal Nacional de Contratações Públicas

            // Observações adicionais
            $table->text('additional_observations')->nullable()->after('pncp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'rg',
                'cpf',
                'company_size',
                'company_activities',
                'opening_date',
                'share_capital',
                'mobile_phone',
                'secondary_email',
                'comprasnet',
                'bec',
                'pregao_eletronico',
                'sicaf',
                'pncp',
                'additional_observations',
            ]);
        });
    }
};
