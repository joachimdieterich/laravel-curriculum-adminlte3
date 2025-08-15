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
        Schema::create('agenda_item_speakers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('agenda_item_id');
            $table->unsignedbigInteger('user_id');;
            $table->string('title');
            $table->timestamps();

            $table->foreign('agenda_item_id')->references('id')->on('agenda_items');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agenda_item_speakers');
    }
};
