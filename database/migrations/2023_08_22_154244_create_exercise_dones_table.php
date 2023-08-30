<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_dones', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('exercise_id');
            $table->smallInteger('iterations')->default(1);
            $table->unsignedbigInteger('user_id');
            $table->unsignedbigInteger('owner_id');

            $table->foreign('exercise_id')->references('id')->on('exercises');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('owner_id')->references('id')->on('users');
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
        Schema::dropIfExists('exercise_dones');
    }
};
