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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('unique_ref')->unique();
            $table->foreignId('source_material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('test')->nullable();
            $table->dateTime('testing_date')->nullable();
            $table->string('description')->nullable();
            $table->float('angle_wrt_source_material')->nullable();
            $table->float('width_mm')->nullable();
            $table->float('height_mm')->nullable();
            $table->float('thickness_mm')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
