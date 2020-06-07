<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reason', 2048);
            $table->unsignedbigInteger('absent_user_id');
            $table->boolean('done')->default(0);
           
            $table->unsignedbigInteger('owner_id');
            
            $table->string('referenceable_type');
            $table->unsignedbigInteger('referenceable_id');
            
            $table->timestamps();
            
            $table->foreign('absent_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('absences');
    }
}
