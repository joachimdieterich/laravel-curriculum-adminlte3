<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnablingObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enabling_objectives', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->text('title');
            
            $table->text('description')->nullable();
            
            $table->string('time_approach')->nullable();
            
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('terminal_objective_id');
            $table->unsignedTinyInteger('order_id');
            
            $table->timestamps();
            
            $table->foreign('curriculum_id')->references('id')->on('curricula');
            $table->foreign('terminal_objective_id')->references('id')->on('terminal_objectives');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enabling_objectives');
    }
}
