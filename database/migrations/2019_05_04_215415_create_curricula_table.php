<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurriculaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            
            $table->string('author');
            $table->string('publisher');
            $table->string('city');
            $table->timestamp('date');
            
            $table->char('color')->default('#3c8dbc99');
            
            
            $table->unsignedbigInteger('grade_id');
            $table->unsignedbigInteger('subject_id');
            $table->unsignedbigInteger('organization_type_id');
            $table->unsignedbigInteger('state_id');
            $table->unsignedbigInteger('country_id');
            
            $table->unsignedbigInteger('file_id');
            
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('organization_type_id')->references('id')->on('organization_types');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('file_id')->references('id')->on('files');
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
        Schema::dropIfExists('curricula');
    }
}
