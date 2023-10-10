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
        Schema::table('videoconferences', function (Blueprint $table) {
            $table->boolean('allJoinAsModerator')->default(false);
            $table->unsignedbigInteger('medium_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videoconferences', function (Blueprint $table) {
            $table->dropColumn('allJoinAsModerator');
            $table->dropColumn('medium_id');
        });
    }
};
