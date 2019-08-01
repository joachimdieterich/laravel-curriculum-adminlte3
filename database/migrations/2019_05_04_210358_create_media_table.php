<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('path');
            $table->string('medium_name');
            $table->string('title');
            $table->text('description');
            $table->string('author');
            $table->string('publisher');
            $table->string('city');
            $table->string('date');
            $table->integer('size');
            $table->string('mime_type');
            
            $table->unsignedbigInteger('license_id');
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('license_id')->references('id')->on('licenses');
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
        Schema::dropIfExists('media');
    }
}
