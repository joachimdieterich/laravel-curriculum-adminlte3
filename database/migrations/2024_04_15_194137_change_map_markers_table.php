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
        Schema::table('map_markers', function (Blueprint $table) {
            $table->string('tags')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->unsignedbigInteger('map_id')->nullable();


            $table->foreign('map_id')->references('id')->on('maps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('map_markers', function (Blueprint $table) {
            $table->dropColumn('map_id');
        });
    }
};
