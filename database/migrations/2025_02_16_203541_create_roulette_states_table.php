<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('roulette_states', function (Blueprint $table) {
            $table->id();
            $table->boolean('spinning')->default(false); // Czy ruletka się kręci
            $table->integer('winning_number')->nullable(); // Numer wygrywający
            $table->integer('randomize')->nullable(); // Losowe przesunięcie
            $table->timestamp('start_time')->nullable(); // Czas rozpoczęcia animacji
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roulette_states');
    }
};
