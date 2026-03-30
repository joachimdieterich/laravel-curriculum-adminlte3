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
        DB::table('achievements')->whereNot('scale_id', 1)->delete();

        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['scale_id']);
            $table->dropColumn('scale_id');
        });

        Schema::drop('achievement_scales');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('achievement_scales', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('achievement_scales')->insert([
            [
                'title' => 'curriculum',
                'description' => 'Default achievement scale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'moodle',
                'description' => 'moodle achievement scale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('achievements', function (Blueprint $table) {
            $table->unsignedBigInteger('scale_id')->default(1); // 1 => curriculum-scale
            $table->foreign('scale_id')->references('id')->on('achievement_scales');
        });
    }
};