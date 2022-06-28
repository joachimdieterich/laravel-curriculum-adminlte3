<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->char('alpha2')->unique();
            $table->char('alpha3')->unique();
            $table->string('langCS');
            $table->string('lang_de');
            $table->string('lang_en');
            $table->string('langES');
            $table->string('langFR');
            $table->string('langIT');
            $table->string('langNL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
