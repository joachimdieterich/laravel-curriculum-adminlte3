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
            $table->text('description')->nullable();
            
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('date')->nullable();
            
            $table->char('color')->default('#3c8dbc99')->nullable();
            
            
            $table->unsignedbigInteger('grade_id');
            $table->unsignedbigInteger('subject_id');
            $table->unsignedbigInteger('organization_type_id');
            $table->char('state_id');
            $table->char('country_id');
            
            $table->unsignedbigInteger('file_id')->nullable();
            
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('state_id')->references('code')->on('states');
            $table->foreign('country_id')->references('alpha2')->on('countries');
            $table->foreign('organization_type_id')->references('id')->on('organization_types');
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
