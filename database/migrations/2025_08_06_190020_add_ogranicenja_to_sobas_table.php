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
        Schema::table('sobas', function (Blueprint $table) {
            $table->string('naziv', 100)->change();
            $table->text('opis')->nullable()->change();
            $table->integer('maksimalan_broj_clanova')->unsigned()->change();
            
            // Dodavanje ograničenja
            $table->check('maksimalan_broj_clanova > 0 AND maksimalan_broj_clanova <= 1000');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sobas', function (Blueprint $table) {
            $table->string('naziv')->change();
            $table->text('opis')->change();
            $table->integer('maksimalan_broj_clanova')->change();
        });
    }
};
