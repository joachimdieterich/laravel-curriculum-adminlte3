<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->bigInteger('grade_id')->unsigned();
            $table->bigInteger('period_id')->unsigned();
            $table->bigInteger('organization_id')->unsigned();
            
            $table->timestamps();
            
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('period_id')->references('id')->on('periods');
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
