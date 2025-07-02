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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->string('type');
            $table->string('badge_label')->nullable();
            $table->string('badge_icon')->nullable();
            $table->string('badge_note')->nullable();
            $table->integer('badge_reward')->default(0);
            $table->integer('badge_counter')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};