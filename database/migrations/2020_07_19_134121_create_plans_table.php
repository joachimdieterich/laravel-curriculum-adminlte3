<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('begin');
            $table->timestamp('end')->nullable();
            $table->integer('duration')->nullable();

            $table->unsignedbigInteger('type_id')->nullable();
            $table->unsignedbigInteger('owner_id')->nullable();

            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('plan_types');
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
