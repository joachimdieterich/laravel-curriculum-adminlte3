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
        Schema::create('agenda_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('agenda_id');
            $table->unsignedbigInteger('agenda_item_type_id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();

            $table->unsignedbigInteger('medium_id')->nullable();

            $table->timestamp('begin')->nullable();
            $table->timestamp('end')->nullable();

            $table->smallInteger('order_id')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('agenda_id')->references('id')->on('agendas');
            $table->foreign('agenda_item_type_id')->references('id')->on('agenda_item_types');
            $table->foreign('medium_id')->references('id')->on('media');
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
        Schema::dropIfExists('agenda_items');
    }
};
