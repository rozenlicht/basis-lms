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
        Schema::create('container_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->integer('compartment_x');
            $table->integer('compartment_y');
            $table->foreignId('sample_id')->nullable()->constrained('samples')->nullOnDelete();
            $table->string('custom_name')->nullable();
            $table->timestamps();
            
            // Ensure unique positions per container
            $table->unique(['container_id', 'compartment_x', 'compartment_y'], 'container_positions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_positions');
    }
};
