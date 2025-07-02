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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->string('category_name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('type')->default(1);
            $table->boolean('is_premium')->default(false);
            $table->integer('coins')->default(0);
            $table->integer('row_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};