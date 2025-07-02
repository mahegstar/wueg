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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->onDelete('set null');
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->text('question');
            $table->tinyInteger('question_type')->default(1)->comment('1:Multiple choice, 2:True/False');
            $table->string('optiona');
            $table->string('optionb');
            $table->string('optionc')->nullable();
            $table->string('optiond')->nullable();
            $table->string('optione')->nullable();
            $table->string('answer');
            $table->integer('level')->default(1);
            $table->text('note')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};