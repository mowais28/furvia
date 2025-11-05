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
        Schema::create('user_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('day'); // e.g. Mondy
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('slot_label')->nullable(); // e.g. slotr 1
            $table->string('month')->nullable(); // e.g. Sep 2025
            $table->integer('week')->nullable(); // e.g. 1
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_availabilities');
    }
};
