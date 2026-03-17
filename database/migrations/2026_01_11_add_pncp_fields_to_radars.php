<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PncpFieldsToRadars extends Migration
{
    public function up(): void
    {
        Schema::table('radars', function (Blueprint $table) {
            $table->string('pncp_id_compra')->nullable()->unique();
            $table->string('numero_controle_pncp')->nullable()->index();
            $table->string('status_pncp')->nullable()->index(); // abertos|fechando|fechado
        });
    }

    public function down(): void
    {
        Schema::table('radars', function (Blueprint $table) {
            $table->dropUnique(['pncp_id_compra']);
            $table->dropColumn(['pncp_id_compra', 'numero_controle_pncp', 'status_pncp']);
        });
    }
}
