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
        Schema::create('videoconference_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('videoconference_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('videoconference_id')->references('id')->on('videoconferences');
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
        Schema::dropIfExists('videoconference_subscriptions');
    }
};
