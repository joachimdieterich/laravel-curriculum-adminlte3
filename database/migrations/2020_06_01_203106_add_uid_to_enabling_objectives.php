<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUidToEnablingObjectives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enabling_objectives', function (Blueprint $table) {
            $table->char('ui', 31);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enabling_objectives', function (Blueprint $table) {
            $table->dropColumn('ui');//
        });
    }
}
