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
            // Altera os campos para permitir NULL
            $table->string('corporate_name')->nullable()->change();
            $table->string('cnpj')->nullable()->change();
            $table->string('trade_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
             // Reverte para NOT NULL (caso precise desfazer)
            $table->string('corporate_name')->nullable(false)->change();
            $table->string('cnpj')->nullable(false)->change();
            $table->string('trade_name')->nullable(false)->change();
        });
    }
};
