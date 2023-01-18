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
        Schema::create('videoconferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('meetingID');
            $table->string('meetingName');
            $table->string('attendeePW');
            $table->string('moderatorPW');
            $table->string('callbackURL');

            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

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
        Schema::dropIfExists('videoconferences');
    }
};
