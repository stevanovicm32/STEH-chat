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
        Schema::table('porukas', function (Blueprint $table) {
            $table->index(['soba_id', 'created_at']);
            $table->index(['korisnik_id', 'created_at']);
            $table->index('je_procitana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('porukas', function (Blueprint $table) {
            $table->dropIndex(['soba_id', 'created_at']);
            $table->dropIndex(['korisnik_id', 'created_at']);
            $table->dropIndex(['je_procitana']);
        });
    }
};
