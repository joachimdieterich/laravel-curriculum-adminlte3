<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculumGroupPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculum_group', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedbigInteger('curriculum_id');
            $table->foreign('curriculum_id')->references('id')->on('curricula');
            $table->unsignedbigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');

            $table->index(['curriculum_id', 'group_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculum_group');
    }
}
