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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('training_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->smallInteger('recommended_iterations')->default(1);

            $table->smallInteger('order_id')->default(0);

            $table->unsignedbigInteger('owner_id');

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('training_id')->references('id')->on('trainings');

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
        Schema::dropIfExists('exercises');
    }
};
