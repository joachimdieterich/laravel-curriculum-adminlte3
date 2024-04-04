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
        Schema::create('map_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('map_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');
            $table->date('due_date')->nullable();
            $table->text('sharing_token')->nullable();
            $table->text('title')->nullable();

            $table->timestamps();

            $table->foreign('map_id')->references('id')->on('maps');
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
        Schema::dropIfExists('map_subscriptions');
    }
};
