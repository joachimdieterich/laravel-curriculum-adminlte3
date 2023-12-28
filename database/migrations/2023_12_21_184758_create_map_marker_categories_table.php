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
        Schema::create('map_marker_categories', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('color');
            $table->string('shape')->default('circle');
            $table->foreignId('parent_id')->nullable()->constrained('map_marker_categories');
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
        Schema::dropIfExists('map_marker_categories');
    }
};
