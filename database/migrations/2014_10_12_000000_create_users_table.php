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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('firebase_id')->nullable();
            $table->string('fcm_id')->nullable();
            $table->string('web_fcm_id')->nullable();
            $table->string('profile')->nullable();
            $table->string('mobile')->nullable();
            $table->string('type')->default('user');
            $table->string('status')->default(1);
            $table->integer('coins')->default(0);
            $table->string('refer_code')->nullable();
            $table->string('friends_code')->nullable();
            $table->string('app_language')->nullable();
            $table->string('web_language')->nullable();
            $table->timestamp('date_registered')->useCurrent();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};