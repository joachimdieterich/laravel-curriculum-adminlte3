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
        Schema::create('meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid');
            $table->string('access_token')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('speakers')->nullable();
            $table->text('info')->nullable();

            $table->timestamp('begin')->nullable();
            $table->timestamp('end')->nullable();
            $table->string('status')->nullable();
            $table->string('category')->nullable();
            $table->json('target_group')->nullable();
            $table->string('url')->nullable();
            $table->string('provider')->nullable();
            $table->unsignedbigInteger('medium_id')->nullable();
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

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
        Schema::dropIfExists('meetings');
    }
};
