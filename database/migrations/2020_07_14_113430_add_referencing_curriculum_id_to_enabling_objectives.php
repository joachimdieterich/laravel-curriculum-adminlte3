<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferencingCurriculumIdToEnablingObjectives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enabling_objectives', function (Blueprint $table) {
            $table->string('referencing_curriculum_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enabling_objectives', function (Blueprint $table) {
            $table->dropColumn('referencing_curriculum_id');
        });
    }
}
