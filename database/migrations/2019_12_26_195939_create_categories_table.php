<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('title');
            
            $table->timestamps();
        });
        
        Schema::create('categorie_content', function (Blueprint $table) {
            $table->bigInteger('categorie_id')->unsigned()->index();
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
            $table->bigInteger('content_id')->unsigned()->index();
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
            
            
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('categorie_content');
    }
}
