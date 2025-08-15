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
        Schema::create('map_markers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedbigInteger('type_id');
            $table->unsignedbigInteger('category_id');
            $table->string('tags');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->string('url')->nullable();

            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('map_marker_types');
            $table->foreign('category_id')->references('id')->on('map_marker_categories');
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
        Schema::dropIfExists('map_markers');
    }
};
