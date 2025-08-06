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
        Schema::create('porukas', function (Blueprint $table) {
            $table->id();
            $table->text('sadrzaj');
            $table->foreignId('korisnik_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('soba_id')->constrained('sobas')->onDelete('cascade');
            $table->string('tip_poruke')->default('tekst'); // tekst, slika, fajl
            $table->string('fajl_putanja')->nullable();
            $table->boolean('je_procitana')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porukas');
    }
};
