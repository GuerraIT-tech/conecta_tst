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
        Schema::table('radars', function (Blueprint $table) {
            $table->foreignId('modality_id')->nullable()->before('id')->constrained('modalities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radars', function (Blueprint $table) {
            $table->dropForeign(['modality_id']);
            $table->dropColumn('modality_id');
        });
    }
};
