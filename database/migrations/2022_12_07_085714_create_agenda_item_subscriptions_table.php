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
        Schema::create('agenda_item_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('agenda_item_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('agenda_item_id')->references('id')->on('agenda_items');
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
        Schema::dropIfExists('agenda_item_subscriptions');
    }
};
