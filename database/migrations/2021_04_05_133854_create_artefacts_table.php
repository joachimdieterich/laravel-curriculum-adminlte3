<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtefactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artefacts', function (Blueprint $table) {

            $table->unsignedbigInteger('medium_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->primary(['medium_id', 'subscribable_type', 'subscribable_id'], 'm_subscr_m_id_subscr_type_subscr_id_primary');

            $table->unsignedbigInteger('user_id');
            $table->timestamps();

            $table->foreign('medium_id')->references('id')->on('media');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artefacts');
    }
}
