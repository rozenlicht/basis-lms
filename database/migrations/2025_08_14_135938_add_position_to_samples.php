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
        Schema::table('samples', function (Blueprint $table) {
            $table->integer('compartment_x')->nullable()->after('id')->comment('Position of the sample in the container');
            $table->integer('compartment_y')->nullable()->after('compartment_x')->comment('Position of the sample in the container');
            $table->foreignId('container_id')->nullable()->constrained('containers')->nullOnDelete()
                ->comment('Reference to the container that holds the sample');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            //
        });
    }
};
