<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('training_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->smallInteger('order_id')->default(0);
            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('trainings');
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
        Schema::dropIfExists('training_subscriptions');
    }
}
