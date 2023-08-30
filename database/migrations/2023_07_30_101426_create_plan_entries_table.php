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
        Schema::create('plan_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('css_icon')->nullable();
            $table->string('color')->nullable();
            $table->unsignedbigInteger('medium_id')->nullable();
            $table->smallInteger('order_id')->default(0);

            $table->timestamps();

            $table->unsignedbigInteger('plan_id');
            $table->unsignedbigInteger('owner_id');

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
        Schema::dropIfExists('plan_entries');
    }
};
