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
        Schema::create('sobas', function (Blueprint $table) {
            $table->id();
            $table->string('naziv')->unique();
            $table->text('opis')->nullable();
            $table->boolean('je_javna')->default(true);
            $table->integer('maksimalan_broj_clanova')->default(50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sobas');
    }
};
