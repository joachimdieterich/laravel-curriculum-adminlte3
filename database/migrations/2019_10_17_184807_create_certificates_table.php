<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('body');

            $table->unsignedbigInteger('curriculum_id');
            $table->unsignedbigInteger('organization_id');
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('curriculum_id')->references('id')->on('curricula');
            $table->foreign('organization_id')->references('id')->on('organizations');
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
        Schema::dropIfExists('certificates');
    }
}
