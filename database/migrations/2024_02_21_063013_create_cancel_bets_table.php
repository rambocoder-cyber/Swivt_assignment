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
        Schema::create('cancel_bets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bet_id');
            $table->timestamps();
            $table->foreign('bet_id')->references('id')->on('bets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancel_bets');
    }
};
