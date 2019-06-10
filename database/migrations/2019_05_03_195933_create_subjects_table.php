<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('title');
            $table->char('title_short')->nullable();
            $table->bigInteger('external_id')->unsigned();
            
            $table->bigInteger('organization_type_id')->unsigned();
            $table->bigInteger('organization_id')->unsigned()->nullable();
            
            $table->timestamps();
            
            $table->foreign('organization_type_id')->references('id')->on('organization_types');
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
        Schema::dropIfExists('subjects');
    }
}
