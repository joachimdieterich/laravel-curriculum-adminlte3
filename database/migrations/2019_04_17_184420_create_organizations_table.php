<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title'); //newer version of institution
            $table->text('description')->nullable();

            $table->string('street')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            
            $table->bigInteger('state_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->unsignedInteger('status'); // newer version of confirmed

            $table->timestamps();
            
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
