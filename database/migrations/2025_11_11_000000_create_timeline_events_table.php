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
        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // e.g., 'created'
            $table->nullableMorphs('subject'); // subject_type, subject_id
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('created_at');
            // Note: nullableMorphs() already creates an index on subject_type and subject_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
    }
};

