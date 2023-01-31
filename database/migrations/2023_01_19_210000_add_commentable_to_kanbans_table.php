<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentableToKanbansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kanbans', function (Blueprint $table) {
            $table->boolean('commentable')->default(0);
            $table->boolean('auto_refresh')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kanbans', function (Blueprint $table) {
            $table->dropColumn('commentable');
            $table->dropColumn('auto_refresh');
        });
    }
}
