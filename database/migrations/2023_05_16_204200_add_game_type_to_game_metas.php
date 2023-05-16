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
        Schema::table('game_metas', function (Blueprint $table) {
            // Drop Individual game types
            $table->dropColumn('dlc');
            $table->dropColumn('video');
            // Add game type column
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_metas', function (Blueprint $table) {
            // Add Individual game types
            $table->string('dlc');
            $table->string('video');
            // Drop game type column
            $table->dropColumn('type');
        });
    }
};
