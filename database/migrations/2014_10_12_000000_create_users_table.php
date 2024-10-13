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
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->unique();
            $table->string('gender');
            $table->string('national_ID')->nullable();
            $table->date('date_of_birth');
            $table->string('occupation');
            $table->string('nationality');
            $table->string('marital_status');
            $table->string('spouse_name')->nullable();
            $table->string('spouse_national_id')->nullable();
            $table->integer('number_of_children')->nullable();
            $table->string('disability');
            $table->string('religion');
            $table->string('education_level');
            $table->string('passport_number')->nullable();
            $table->foreignId('family_id')->nullable()->constrained('families');
            $table->boolean('is_head_of_family')->default(false);
            $table->unsignedBigInteger('village_id');
            $table->foreign('village_id')->references('id')->on('villages');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
