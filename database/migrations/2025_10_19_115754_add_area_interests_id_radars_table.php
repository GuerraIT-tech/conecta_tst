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
            $table->foreignId('area_interest_id')->nullable()->after('titulo')->constrained('area_interests')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radars', function (Blueprint $table) {
            $table->dropForeign(['area_interest_id']);
            $table->dropColumn('area_interest_id');
        });
    }
};
