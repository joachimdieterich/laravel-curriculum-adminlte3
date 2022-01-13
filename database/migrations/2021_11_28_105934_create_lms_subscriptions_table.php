<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_subscriptions', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            $table->string('repository'); //eg 'moodle'
            $table->text('value');
            $table->unsignedbigInteger('sharing_level_id');
            $table->boolean('visibility');
            $table->unsignedbigInteger('owner_id');
            $table->timestamps();

            $table->foreign('sharing_level_id')->references('id')->on('sharing_levels');
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
        Schema::dropIfExists('lms_subscriptions');
    }
}
