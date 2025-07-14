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
        Schema::create('source_materials', function (Blueprint $table) {
            $table->id();
            $table->string('unique_ref')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('grade')->nullable();
            $table->string('supplier')->nullable();
            $table->string('supplier_identifier')->nullable();
            $table->float('width_mm')->nullable();
            $table->float('height_mm')->nullable();
            $table->float('thickness_mm')->nullable();
            $table->json('properties')->nullable();
            $table->json('composition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_materials');
    }
};
