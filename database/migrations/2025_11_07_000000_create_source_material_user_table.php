<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_material_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['source_material_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_material_user');
    }
};

