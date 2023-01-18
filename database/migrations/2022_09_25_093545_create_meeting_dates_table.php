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
        Schema::create('meeting_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid');
            $table->unsignedbigInteger('meeting_id');
            $table->string('access_token')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('address')->nullable();

            $table->timestamp('begin')->nullable();
            $table->timestamp('end')->nullable();

            $table->string('type');

            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('meetings');
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
        Schema::dropIfExists('meeting_dates');
    }
};
