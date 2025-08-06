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
        Schema::create('clan_sobes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korisnik_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('soba_id')->constrained('sobas')->onDelete('cascade');
            $table->enum('uloga', ['admin', 'moderator', 'clan'])->default('clan');
            $table->boolean('je_aktivan')->default(true);
            $table->timestamp('poslednja_aktivnost')->nullable();
            $table->timestamps();
            
            // Jedinstvena kombinacija korisnika i sobe
            $table->unique(['korisnik_id', 'soba_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clan_sobes');
    }
};
