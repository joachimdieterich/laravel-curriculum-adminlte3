<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerminalObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminal_objectives', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->text('title'); 
            
            $table->text('description')->nullable();
            
            $table->char('color', 9)->default('#008000');
            $table->string('time_approach')->nullable();
            
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('objective_type_id');
            $table->unsignedTinyInteger('order_id');
            
            $table->timestamps();
            
            $table->foreign('curriculum_id')->references('id')->on('curricula');
            $table->foreign('objective_type_id')->references('id')->on('objective_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terminal_objectives');
    }
}
