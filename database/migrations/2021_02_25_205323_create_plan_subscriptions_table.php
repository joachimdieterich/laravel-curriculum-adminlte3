<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('plan_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans');
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
        Schema::dropIfExists('plan_subscriptions');
    }
}
