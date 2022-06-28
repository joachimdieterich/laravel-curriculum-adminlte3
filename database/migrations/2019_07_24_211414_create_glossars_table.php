<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlossarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('glossars', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            $table->index(['subscribable_type', 'subscribable_id']);
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
        Schema::dropIfExists('glossars');
    }
}
