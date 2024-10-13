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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('citizen_id');
            $table->foreignId('from_village_id');
            $table->foreignId('to_village_id');
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->uuid('approved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('citizen_id')->references('id')->on('users');
            $table->foreign('from_village_id')->references('id')->on('villages');
            $table->foreign('to_village_id')->references('id')->on('villages');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
