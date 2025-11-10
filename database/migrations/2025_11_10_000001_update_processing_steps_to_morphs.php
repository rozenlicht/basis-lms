<?php

use App\Models\Sample;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processing_steps', function (Blueprint $table) {
            $table->nullableMorphs('processable');
        });

        DB::table('processing_steps')
            ->select(['id', 'sample_id'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    if (! $row->sample_id) {
                        continue;
                    }

                    DB::table('processing_steps')
                        ->where('id', $row->id)
                        ->update([
                            'processable_type' => Sample::class,
                            'processable_id' => $row->sample_id,
                        ]);
                }
            }, 'id');

        Schema::table('processing_steps', function (Blueprint $table) {
            $table->dropForeign(['sample_id']);
            $table->dropColumn('sample_id');
        });
    }

    public function down(): void
    {
        Schema::table('processing_steps', function (Blueprint $table) {
            $table->foreignId('sample_id')
                ->nullable()
                ->after('id')
                ->constrained('samples')
                ->cascadeOnDelete();
        });

        DB::table('processing_steps')
            ->select(['id', 'processable_id', 'processable_type'])
            ->where('processable_type', Sample::class)
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('processing_steps')
                        ->where('id', $row->id)
                        ->update([
                            'sample_id' => $row->processable_id,
                        ]);
                }
            }, 'id');

        Schema::table('processing_steps', function (Blueprint $table) {
            $table->dropMorphs('processable');
        });
    }
};

