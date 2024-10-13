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
        Schema::table('transfer_requests', function (Blueprint $table) {
            //
            Schema::table('transfer_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('province_id')->after("from_village_id")->nullable();
                $table->unsignedBigInteger('district_id')->nullable();
                $table->unsignedBigInteger('sector_id')->nullable();
                $table->unsignedBigInteger('cell_id')->nullable();
                
                // Add foreign key constraints if needed
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
                $table->foreign('cell_id')->references('id')->on('cells')->onDelete('set null');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            //
        });
    }
};
