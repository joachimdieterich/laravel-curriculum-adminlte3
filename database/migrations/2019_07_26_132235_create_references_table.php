<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('references', function (Blueprint $table) {
            $table->char('id')->unique();
            $table->text('description')->nullable();
            $table->unsignedbigInteger('grade_id');
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('grade_id')->references('id')->on('grades');
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
        Schema::dropIfExists('references');
    }
}
