<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedbigInteger('task_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            $table->timestamp('completion_date')->nullable();
            
            $table->unsignedbigInteger('owner_id');
            $table->timestamps();
            
            $table->foreign('task_id')->references('id')->on('tasks');
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
        Schema::dropIfExists('task_subscriptions');
    }
}