<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing samples with container assignments to container_positions
        $samplesWithContainers = DB::table('samples')
            ->whereNotNull('container_id')
            ->whereNotNull('compartment_x')
            ->whereNotNull('compartment_y')
            ->get();

        foreach ($samplesWithContainers as $sample) {
            DB::table('container_positions')->insert([
                'container_id' => $sample->container_id,
                'compartment_x' => $sample->compartment_x,
                'compartment_y' => $sample->compartment_y,
                'sample_id' => $sample->id,
                'custom_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all container_positions that have sample_id
        DB::table('container_positions')->whereNotNull('sample_id')->delete();
    }
};
