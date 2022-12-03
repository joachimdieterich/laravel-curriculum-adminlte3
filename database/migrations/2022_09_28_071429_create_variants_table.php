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
        Schema::create('variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('variant_definition_id');
            $table->text('title');
            $table->text('description')->nullable();

            $table->string('referenceable_type');
            $table->unsignedbigInteger('referenceable_id');

            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('variant_definition_id')->references('id')->on('variant_definitions');
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
        Schema::dropIfExists('variants');
    }
};
